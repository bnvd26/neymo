<?php

namespace App\Controller\API;

use App\Repository\CurrencyRepository;
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
     * @param CurrencyService $currencyService
     */
    public function convertToEuro(
        CurrencyService $currencyService,
        Request $request,
        CurrencyRepository $currencyRepository
    ): JsonResponse {
        $value = (int) $request->get("value");
        $currencyId = (int) $request->get('currency');
        $currency = $currencyRepository->find($currencyId);
        if (null === $currency) {
            return new JsonResponse([
                "status" => "error",
                "error" => "Currency not found"
            ]);
        }
        $convertedValue = $currencyService->convertToEuro(
            $currency->getExchangeRate(),
            $value
        );

        return new JsonResponse($convertedValue);
    }
}
