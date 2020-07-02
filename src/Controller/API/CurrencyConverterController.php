<?php

namespace App\Controller\API;

use App\Services\CurrencyService;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/currency-converter", name="api_currency_converter_")
 */
class CurrencyConverterController extends AbstractController
{
    /**
     * @Route("/to-euro", name="to_euro", methods="POST")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the rewards of an user"
     * )
     * @SWG\Parameter(
     *     name="value",
     *     in="query",
     *     type="number",
     *     description="The field contains the amount of currency we want to convert"
     * )
     * @SWG\Parameter(
     *     name="currency",
     *     in="query",
     *     type="number",
     *     description="The field contains the currency id"
     * )
     * @SWG\Tag(name="currency")
     *
     * @param CurrencyService $currency
     */
    public function convertToEuro(CurrencyService $currency, Request $request): JsonResponse
    {

    }
}
