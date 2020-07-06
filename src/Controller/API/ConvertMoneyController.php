<?php

namespace App\Controller\API;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConvertMoneyController extends ApiController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/api/convert-money", name="api_convert_money", methods="PUT")
     */
    public function convertMoney(Request $request)
    {
        $right_card = [
            'numbers_card' => '1111 1111 1111 1111',
            'date' => '11/11/20',
            'cvc' => '456',
            'transfered_money' => '1000'
        ];

        if ($this->getUser()->isParticular()) {
            $data = json_decode($request->getContent());
            
            if ($right_card['numbers_card'] === $data->numbers_card && $right_card['date'] === $data->date && $right_card['cvc'] === $data->cvc) {
                $account = $this->getUser()->getParticular()->getAccount();
                
                $account->setAvailableCash((int) $data->transfered_money + (int) $account->getAvailableCash());

                $this->em->persist($account);

                $this->em->flush();

                return $this->responseOk(['Success' => "Votre argent à bien été ajouté"]);
            }      
        }

        if ($this->getUser()->isCompany()) {
            $data = json_decode($request->getContent());
            
            if ($right_card['numbers_card'] === $data->numbers_card && $right_card['date'] === $data->date && $right_card['cvc'] === $data->cvc) {
                $account = $this->getUser()->getCompany()->getAccount();
                
                $account->setAvailableCash((int) $data->transfered_money + (int) $account->getAvailableCash());

                $this->em->persist($account);

                $this->em->flush();

                return $this->responseOk(['Success' => "Votre argent à bien été ajouté"]);
            }            
        }

        return $this->responseOk(['Error' => "Vous n'avez pas saisis les bonnes informations de votre carte"]);
    }
}
