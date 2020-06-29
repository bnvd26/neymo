<?php

namespace App\Controller\API;

use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\AccountRepository;
use App\Repository\CompanyRepository;
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
     * @Route("/api/transactions", name="api_transactions", methods="GET")
     */
    public function allTransactions()
    {
        $p = [];
        foreach ($this->getUser()->getParticular()->getAccount()->getTransactions() as $transaction) {
            $p[] = [
                'transfered_money' => $transaction->getTransferedMoney(),
            ];
        }
        dd($p);
        
        $t = [];
        foreach ($this->getUser()->getCompanies() as $company) {
            foreach ($company->getAccount()->getTransactions() as $transaction) {
                $t[] = $transaction->getTransferedMoney();
            }
        }
        dd($t);
    }

    /**
     * @Route("/api/transfer-money", name="api_transfer_money", methods="POST")
     */
    public function transferMoney(Request $request, AccountRepository $accountRepository)
    {
        $data = json_decode($request->getContent());

        if ( (int) $accountRepository->find($data->emiterAccountId)->getAvailableCash() < (int) $data->transferedMoney) {
            $response = new Response();
            $response->setStatusCode(Response::HTTP_OK);

            $response->setContent(json_encode([
            'Error' => "ya probleme d'argent la",
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
}
