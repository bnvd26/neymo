<?php

namespace App\Controller\API;

use App\Entity\Transaction;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;

class ConvertMoneyController extends ApiController
{
    private const CREDIT_CARD = [
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
     *
     * @SWG\Response(
     *     response=200,
     *     description="Credit card information checked"
     * )
     * @SWG\Response(
     *     response=405,
     *     description="Credit card information invalid"
     * )
     * @SWG\Parameter(
     *      name="Authorization",
     *      in="header",
     *      required=true,
     *      type="string",
     *      default="Bearer TOKEN",
     *      description="Bearer token",
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Object describing the credit card.",
     *     required=true,
     *     @SWG\Schema(
     *      @SWG\Property(property="numbers_card", type="string", example="1111 1111 1111 1111"),
     *      @SWG\Property(property="date", type="string", example="11/11/20"),
     *      @SWG\Property(property="cvc", type="int", example="456")
     *     )
     * )
     * @SWG\Tag(name="convert-currency")
     *
     * @param Request $request
     *
     * @return object|Response
     */
    public function checkCreditCard(Request $request)
    {
        $data = json_decode($request->getContent());

        return $this->verificationCreditCard($data) ? $this->responseOk(['Success' => 'La carte est validé']) : $this->responseNotAllowed(['Error' => 'La carte est refusé']);
    }

    public function verificationCreditCard($data)
    {
        return self::CREDIT_CARD['numbers_card'] === $data->numbers_card && self::CREDIT_CARD['date'] === $data->date && self::CREDIT_CARD['cvc'] === $data->cvc;
    }

    /**
     * @Route("/api/convert-money", name="api_convert_money", methods="PUT")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Amount of conversion added to account"
     * )
     * @SWG\Parameter(
     *      name="Authorization",
     *      in="header",
     *      required=true,
     *      type="string",
     *      default="Bearer TOKEN",
     *      description="Bearer token",
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Object describing the amount.",
     *     required=true,
     *     @SWG\Schema(
     *      @SWG\Property(property="transfered_money", type="int", example="1230")
     *     )
     * )
     * @SWG\Tag(name="convert-currency")
     *
     * @param Request $request
     *
     * @return object|Response
     */

    public function convertMoney(Request $request)
    {
        $data = json_decode($request->getContent());

        $userTypeAccount = $this->getUser()->isParticular() ? $this->getUser()->getParticular()->getAccount() : $this->getUser()->getCompany()->getAccount();

        if ($this->verificationCreditCard($data)) {
            $userTypeAccount->setAvailableCash((int) $data->transfered_money + (int) $userTypeAccount->getAvailableCash());

            $transaction  = new Transaction();

            $transaction->setBeneficiary($userTypeAccount);
            $transaction->setTransferedMoney($data->transfered_money);
            $transaction->setDate(new DateTime());

            $this->em->persist($transaction);
            $this->em->persist($userTypeAccount);

            $this->em->flush();

            return $this->responseOk(['Success' => "Votre argent à bien été ajouté"]);
        }

        return $this->responseNotAllowed(['Error' => "Vous n'avez pas saisis les bonnes informations de votre carte"]);
    }
}
