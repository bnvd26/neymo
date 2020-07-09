<?php

namespace App\Controller\API;

use App\Repository\CurrencyRepository;
use App\Services\CreditCardService;
use App\Services\CurrencyService;
use Exception;
use Swagger\Annotations as SWG;
use App\Controller\API\ApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/currency-converter", name="api_currency_converter_")
 */
class CurrencyConverterController extends ApiController
{
    /**
     * @Route("/to-euro", name="to_euro", methods="POST")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the conversion of the amount in euro"
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
     *     description="The amount to convert",
     *     required=true,
     *     @SWG\Schema(
     *      @SWG\Property(property="transferedMoney", type="int", example="1000")
     *     )
     * )
     * @SWG\Tag(name="currency")
     *
     * @param CurrencyService $currencyService
     * @param Request $request
     *
     * @throws Exception
     *
     * @return Response*
     */
    public function convertToEuro(CurrencyService $currencyService,Request $request) 
    {
        if ($this->getUser()->isParticular()) {
            return $this->responseNotAllowed([
                "Error" => "Vous n'etes pas autorise a effectuer ce genre de transaction"
            ]);
        }

        $exchangeRate = $this->getUser()->getCompany()->getGovernance()->getCurrency()->getExchangeRate();
        $data = json_decode($request->getContent());
        $value = $data->transferedMoney;
        
        $convertedValue = $currencyService->convertToEuro(
            $exchangeRate,
            $value
        );

        return $this->responseOk($convertedValue);
    }
}
