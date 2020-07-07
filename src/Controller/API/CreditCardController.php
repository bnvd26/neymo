<?php

namespace App\Controller\API;

use App\Services\CreditCardService;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/cc", name="api_cc_")
 */
class CreditCardController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    /**
     * @Route("/check", name="check", methods="POST")
     *
     * @SWG\Parameter(
     *     name="card-number",
     *     in="query",
     *     type="number",
     *     description="The credit card number"
     * )
     *
     * @SWG\Parameter(
     *     name="card-type",
     *     in="query",
     *     type="string",
     *     description="The credit card type"
     * )
     *
     * @SWG\Parameter(
     *     name="cvc",
     *     in="query",
     *     type="string",
     *     description="The credit card cvc"
     * )
     *
     * @SWG\Parameter(
     *     name="month",
     *     in="query",
     *     type="string",
     *     description="The credit card expiration month"
     * )
     *
     * @SWG\Parameter(
     *     name="year",
     *     in="query",
     *     type="string",
     *     description="The credit card expiration year"
     * )
     *
     * @SWG\Parameter(
     *     name="card-holder-name",
     *     in="query",
     *     type="string",
     *     description="The credit card holder name"
     * )
     *
     * @SWG\Tag(name="credit-card")
     * 
     * @SWG\Response(response="201", description="Je suis une réponse")
     *
     * @param CreditCardService $creditCardService
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function checkCreditCard(CreditCardService $creditCardService, Request $request)
    {
        $cardNumber = $request->get("card-number");
        $cardType = $request->get("card-type");
        $cvc = $request->get("cvc");
        $month = $request->get("month");
        $year = $request->get("year");
        $cardHolderName = $request->get("card-holder-name");
        if ($creditCardService->checkItAll(
            $cardNumber,
            $cardType,
            $cvc,
            $cardHolderName,
            $year,
            $month
        )) {
            return new JsonResponse([
                "status" => "error",
                "error" => "Information is invalid"
            ], Response::HTTP_NOT_ACCEPTABLE);
        } else {
            return new JsonResponse([
                "status" => "success",
                "message" => "Information is valid"
            ], Response::HTTP_OK);
        }
    }
}
