<?php

namespace App\Controller\API;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConvertMoneyController extends ApiController
{

    private CONST CREDIT_CARD = [
        'numbers_card' => '1111 1111 1111 1111',
        'date' => '11/20',
        'cvc' => '456',
        'transfered_money' => '1000'
    ];

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/api/check-credit-card", name="api_check_credit_card", methods="POST")
     */
    public function checkCreditCard(Request $request)
    {
        $data = json_decode($request->getContent());

            if (self::CREDIT_CARD['numbers_card'] === $data->numbers_card && self::CREDIT_CARD['date'] === $data->date && self::CREDIT_CARD['cvc'] === $data->cvc) {
               
                return $this->responseOk(['Success' => 'La carte est validé']);
            }
        
            return $this->responseNotAllowed(['Error' => 'La carte est refusé']);
    }

    /**
     * @Route("/api/convert-money", name="api_convert_money", methods="PUT")
     */
    public function convertMoney(Request $request)
    {
        $data = json_decode($request->getContent());

        if ($this->getUser()->isParticular()) {          
                $account = $this->getUser()->getParticular()->getAccount();
                
                $account->setAvailableCash((int) $data->transfered_money + (int) $account->getAvailableCash());

                $this->em->persist($account);

                $this->em->flush();

                return $this->responseOk(['Success' => "Votre argent à bien été ajouté"]);
            }      
        
        if ($this->getUser()->isCompany()) {
                $account = $this->getUser()->getCompany()->getAccount();
                
                $account->setAvailableCash((int) $data->transfered_money + (int) $account->getAvailableCash());

                $this->em->persist($account);

                $this->em->flush();

                return $this->responseOk(['Success' => "Votre argent à bien été ajouté"]);
            
        }

        return $this->responseOk(['Error' => "Vous n'avez pas saisis les bonnes informations de votre carte"]);
    }
}
