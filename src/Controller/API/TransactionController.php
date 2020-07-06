<?php

namespace App\Controller\API;

use App\Entity\Transaction;
use App\Repository\AccountRepository;
use App\Repository\TransactionRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\API\ApiController;
use Doctrine\ORM\EntityManagerInterface;

class TransactionController extends ApiController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/api/transfer-money", name="api_transfer_money", methods="POST")
     */
    public function transferMoney(Request $request, AccountRepository $accountRepository)
    {
        $data = json_decode($request->getContent());

        $emitterId = null;

        if($this->getUser()->isCompany()) {
            $emitterId = $this->getUser()->getCompany()->getAccount()->getId();
            if ((int) $accountRepository->find($emitterId)->getAvailableCash() < (int) $data->transferedMoney || (int) $data->transferedMoney < 0) {
                return $this->responseOk(['Error' => "Vous n'avez pas les fonds necéssaires pour transférer de l'argent"]);
            }
           
        }

        if($this->getUser()->isParticular()) {
            $emitterId = $this->getUser()->getParticular()->getAccount()->getId();
            if ((int) $accountRepository->find($emitterId)->getAvailableCash() < (int) $data->transferedMoney || (int) $data->transferedMoney < 0) {
                return $this->responseOk(['Error' => "Vous n'avez pas les fonds necéssaires pour transférer de l'argent"]);
            }
        }

        $transaction = new Transaction();
        $beneficiaryAccountId = $accountRepository->findBy(['account_number' => $data->beneficiaryAccountNumber])[0]->getId();
        $transaction->setBeneficiary($accountRepository->find($beneficiaryAccountId));
        $accountRepository->find($beneficiaryAccountId)->addMoneyToBeneficiary($data->transferedMoney);

        $transaction->setEmiter($accountRepository->find($emitterId));
        $accountRepository->find($emitterId)->removeMoneyToEmiter($data->transferedMoney);
        
        $transaction->setTransferedMoney($data->transferedMoney);
        $transaction->setDate(new \DateTime());
        
        
        $this->em->persist($transaction);
        $this->em->flush();

        return $this->responseCreated([
            'Success' => "Argent bien envoyé",
            ]);
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
                $data[] = [
                        'id' => $transactionsByDate[$y]->getId(),
                        'transfered_money' => $transactionsByDate[$y]->getTransferedMoney(),
                        'date' => $transactionsByDate[$y]->getDate(),
                        'status_transaction_user' => $transactionsByDate[$y]->getBeneficiary()->getId() == $this->getUser()->getParticular()->getAccount()->getId() ? 'beneficiary' : 'emiter'
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
                $data[] = [
                        'id' => $transactionsByDate[$y]->getId(),
                        'transfered_money' => $transactionsByDate[$y]->getTransferedMoney(),
                        'date' => $transactionsByDate[$y]->getDate(),
                        'status_transaction_user' => $transactionsByDate[$y]->getBeneficiary()->getId() == $this->getUser()->getCompany()->getAccount()->getId() ? 'beneficiary' : 'emiter'
                ];
            };
            $transactions[] = [
                'date' => $transactionsFrenchDate[$key],
                'transaction' => $data
            ];
        }

        return $this->responseOk($transactions);
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
