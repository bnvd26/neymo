<?php

namespace App\Controller\API;

use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\AccountRepository;
use App\Repository\CompanyRepository;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TransactionController extends AbstractController
{
    private function serialize($data)
    {
        return $this->container->get('serializer')->serialize($data, 'json');
    }

    private function deserialize($data, $entity)
    {
        return $this->container->get('serializer')->deserialize($data, $entity, 'json');
    }


    /**
     * @Route("/api/transactions", name="api_transactions_particular", methods="GET")
     */
    public function allTransactions(TransactionRepository $transactionRepository)
    {
        if ($this->getUser()->isParticular()) {
            return $this->getTransactionsForParticular($transactionRepository);
        } else {
            return $this->getTransactionsForCompany($transactionRepository);
        };
    }

    /**
     * @Route("/api/transfer-money", name="api_transfer_money", methods="POST")
     */
    public function transferMoney(Request $request, AccountRepository $accountRepository)
    {
        $data = json_decode($request->getContent());
    
        if ((int) $accountRepository->find($data->emiterAccountId)->getAvailableCash() < (int) $data->transferedMoney || (int) $data->transferedMoney < 0) {
            $response = new Response();
            $response->setStatusCode(Response::HTTP_OK);
            $response->setContent(json_encode([
                'Error' => "Vous n'avez pas les fonds necéssaires pour transférer de l'argent",
            ]));
            
            $response->headers->set('Content-Type', 'application/json');
            
            return $response;
        }

        $transaction = new Transaction();
        $transaction->setBeneficiary($accountRepository->find($data->beneficiaryAccountId));
        $transaction->setEmiter($accountRepository->find($data->emiterAccountId));
        $accountRepository->find($data->emiterAccountId)->removeMoneyToEmiter($data->transferedMoney);
        $accountRepository->find($data->beneficiaryAccountId)->addMoneyToBeneficiary($data->transferedMoney);
        $transaction->setTransferedMoney($data->transferedMoney);
        $transaction->setDate(new \DateTime());
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($transaction);
        $entityManager->flush();
        $response = new Response();

        $response->setStatusCode(Response::HTTP_OK);

        $response->setContent(json_encode([
            'Success' => "Argent bien envoyé",
            ]));
            
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;

    }

    public function getTransactionsForParticular($transactionRepository)
    {
        $transactionsDateFormatted = [];

        $transactionsFrenchDate = [];

        foreach ($transactionRepository->findAllTransactions($this->getUser()->getParticular()->getAccount()->getId()) as $transaction) {
            $transactionsFrenchDate[] = $this->dateToFrench(date_format($transaction->getDate(), 'Y-m-d H:i:s'), 'l j F');
            $transactionsDateFormatted[] =  date($transaction->getDate()->format('Y-m-d H:i:s'));
        }

        $transactions = [];

        foreach (array_unique($transactionsDateFormatted) as $key => $date) {
            $transactionsByDate = $transactionRepository->findTransactionsByDate(date_create_from_format('Y-m-d H:i:s', $date), $this->getUser()->getParticular()->getAccount()->getId());
            $data = [];
            for ($y = 0; $y < count($transactionsByDate); $y++) {
                $data[] = ['id' => $transactionsByDate[$y]->getId(),
                        'transfered_money' => $transactionsByDate[$y]->getTransferedMoney()
                ];
            };
            $transactions[] = [
                'date' => $transactionsFrenchDate[$key],
                'transaction' => $data
            ];
        }

        return $this->responseOk($transactions);
    }

    public function getTransactionsForCompany($transactionRepository)
    {
        $transactionsDateFormatted = [];

        $transactionsFrenchDate = [];

        $companyId = null;

        foreach ($this->getUser()->getCompanies() as $company) {
            $companyId = $company->getAccount()->getId();
        }
        
        foreach ($transactionRepository->findAllTransactions($companyId) as $transaction) {
            $transactionsFrenchDate[] = $this->dateToFrench(date_format($transaction->getDate(), 'Y-m-d H:i:s'), 'l j F');
            $transactionsDateFormatted[] =  date($transaction->getDate()->format('Y-m-d H:i:s'));
        }

        $transactions = [];

        foreach (array_unique($transactionsDateFormatted) as $key => $date) {
            $transactionsByDate = $transactionRepository->findTransactionsByDate(date_create_from_format('Y-m-d H:i:s', $date), $companyId);
            $data = [];
            for ($y = 0; $y < count($transactionsByDate); $y++) {
                $data[] = ['id' => $transactionsByDate[$y]->getId(),
                        'transfered_money' => $transactionsByDate[$y]->getTransferedMoney(),
                ];
            };
            $transactions[] = [
                'date' => $transactionsFrenchDate[$key],
                'transaction' => $data
            ];
        }

        return $this->responseOk($transactions);
    }

    public function responseOk($data)
    {
        $response = new Response();

        $response->setStatusCode(Response::HTTP_OK);

        $response->setContent(json_encode($data));
            
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }

    public static function dateToFrench($date, $format)
    {
        $english_days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $french_days = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
        $english_months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        $french_months = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
        return str_replace($english_months, $french_months, str_replace($english_days, $french_days, date($format, strtotime($date))));
    }
}
