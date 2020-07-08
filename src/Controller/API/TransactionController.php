<?php

namespace App\Controller\API;

use App\Entity\Transaction;
use App\Repository\AccountRepository;
use App\Repository\TransactionRepository;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\API\ApiController;
use App\Repository\CurrencyRepository;
use App\Services\CreditCardService;
use App\Services\CurrencyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends ApiController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/api/transfer-money", name="api_transfer_money", methods="POST")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Transfer money from an account to an other"
     * )
     * @SWG\Parameter(
     *     name="emiterAccountId",
     *     in="query",
     *     type="number",
     *     description="The account id of the emiter of the transaction"
     * )
     * @SWG\Parameter(
     *     name="beneficiaryAccountId",
     *     in="query",
     *     type="number",
     *     description="The account id of the beneficiary of the transaction"
     * )
     * @SWG\Parameter(
     *     name="transferedMoney",
     *     in="query",
     *     type="number",
     *     description="The amount of the transaction"
     * )
     * @SWG\Tag(name="transaction")
     *
     * @param Request $request
     * @param AccountRepository $accountRepository
     *
     * @return Response
     */
    public function transferMoney(Request $request, AccountRepository $accountRepository)
    {
        $data = json_decode($request->getContent());
        
        $emitterId = $this->getUser()->isParticular() ? $this->getUser()->getParticular()->getAccount()->getId() : $this->getUser()->getCompany()->getAccount()->getId();

        if ((int) $accountRepository->find($emitterId)->getAvailableCash() < (int) $data->transferedMoney ||(int) $data->transferedMoney < 0) {
            return $this->responseOk(['Error' => "Vous n'avez pas les fonds necéssaires pour transférer de l'argent"]);
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
     * @Route("api/convertToEuro", name="api_converToEuro", methods="POST")
     *
     * @SWG\Response(
     *     response=201,
     *     description="Amount transfered"
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Expected Json"
     * )
     * @SWG\Response(
     *     response=406,
     *     description="User not authorized"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Accounts information is invalid"
     * )
     * @SWG\Parameter(
     *      name="Authorization",
     *      in="header",
     *      required=true,
     *      type="string",
     *      default="Bearer TOKEN",
     *      description="Bearer token",
     * )
     *   @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     description="Object describing the currency conversion.",
     *     required=true,
     *     @SWG\Schema(
     *      @SWG\Property(property="value", type="int", example="200"),
     *      @SWG\Property(property="currency", type="int", example="1"),
     *      @SWG\Property(property="emiterAccountId", type="int", example="107")
     *     )
     * )
     * @SWG\Tag(name="transaction")
     *
     * @param CurrencyService $currencyService
     * @param Request $request
     * @param CurrencyRepository $currencyRepository
     * @param AccountRepository $accountRepository
     *
     * @throws Exception
     *
     * @return JsonResponse
     */
    public function convertToEuro(
        CurrencyService $currencyService,
        Request $request,
        CurrencyRepository $currencyRepository,
        AccountRepository $accountRepository
    ): JsonResponse {
        $payload = json_decode($request->getContent());
        if (null === $payload) {
            return new JsonResponse([
                "status" => "error",
                "error" => "Expected Json, gat 🤷‍♂️"
            ], Response::HTTP_BAD_REQUEST);
        }
        $expectedParams = [
            "value",
            "currency",
            "emiterAccountId"
        ];
        foreach ($expectedParams as $expectedParam) {
            if (!isset($payload->$expectedParam)){
                return new JsonResponse([
                    "status" => "error",
                    "error" => "Mandatory parameters not present"
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        $currency = $currencyRepository->find($payload->currency);

        $convertedValue = $currencyService->convertToEuro(
            $currency->getExchangeRate(),
            $payload->value
        );
        if ($this->getUser()->isParticular()) {
            return new JsonResponse([
                "status" => "error",
                "error" => "User not authorized"
            ], Response::HTTP_NOT_ACCEPTABLE);
        }
        $transaction = new Transaction();
        $emiterAccount = $accountRepository->find($payload->emiterAccountId);
        if (null === $emiterAccount) {
            return new JsonResponse([
                "status" => "error",
                "error" => "Accounts information is invalid"
            ], Response::HTTP_NOT_FOUND);
        }
        if ($payload->value > $emiterAccount->getAvailableCash()) {
            return new JsonResponse([
                "status" => "error",
                "error" => "Not enough cash"
            ], Response::HTTP_NOT_ACCEPTABLE);
        }
        $transaction->setEmiter($emiterAccount);
        $accountRepository->find($payload->emiterAccountId)->removeMoneyToEmiter($payload->value);
        $transaction->setTransferedMoney($payload->value);
        $transaction->setDate(new \DateTime());
        $this->em->persist($transaction);
        $this->em->flush();
        $response = new JsonResponse();
        $response->setStatusCode(Response::HTTP_CREATED);
        $response->setData([
            'status' => 'success',
            'message' => 'Votre argent a bien été transféré'
        ]);

        return $response;
    }

    /**
     * @Route("/api/transactions", name="api_transactions_particular", methods="GET")
     */
    public function allTransactions(TransactionRepository $transactionRepository)
    {
        $currentAccountId = $this->getUser()->isParticular() ? $this->getUser()->getParticular()->getAccount()->getId() : $this->getUser()->getCompany()->getAccount()->getId();
        
        return $this->getTransactionsTest($transactionRepository, $currentAccountId);
    }

    public function getTransactionsTest($transactionRepository, $accountId)
    {
        $transactionsDateFormatted = [];
        $transactionsFrenchDate = [];
      
        foreach ($transactionRepository->findAllTransactions($accountId) as $transaction) {
            $transactionsFrenchDate[] = $this->dateToFrench(date_format($transaction->getDate(), 'Y-m-d H:i:s'), 'l j F');
            $transactionsDateFormatted[] =  date($transaction->getDate()->format('Y-m-d H:i:s'));
        }
        $transactions = [];
        foreach (array_unique($transactionsDateFormatted) as $key => $date) {
            $transactionsByDate = $transactionRepository->findTransactionsByDate(date_create_from_format('Y-m-d H:i:s', $date), $accountId);
            $data = [];
            for ($y = 0; $y < count($transactionsByDate); $y++) {
                $data[] = [
                        'id' => $transactionsByDate[$y]->getId(),
                        'transfered_money' => $transactionsByDate[$y]->getTransferedMoney(),
                        'beneficiary_name' => is_null($transactionsByDate[$y]->getBeneficiary()->getParticular()) ? $transactionsByDate[$y]->getBeneficiary()->getCompany()->getName() : $transactionsByDate[$y]->getBeneficiary()->getParticular()->getFirstName() . " " . $transactionsByDate[$y]->getBeneficiary()->getParticular()->getLastName(),
                        'date' => $transactionsByDate[$y]->getDate(),
                        'category' => is_null($transactionsByDate[$y]->getBeneficiary()->getParticular()) ? $transactionsByDate[$y]->getBeneficiary()->getCompany()->getCategory()->getName() : null,
                        'status_transaction_user' => $this->getStatusTransactionUser($transactionsByDate, $y)
                ];
            };
            $transactions[] = [
                'date' => $transactionsFrenchDate[$key],
                'transaction' => $data
            ];
        }

        return $this->responseOk($transactions);
    }

    public function getStatusTransactionUser($transactionsByDate, $y)
    {
        if ($this->getUser()->isParticular()) {
            return $transactionsByDate[$y]->getBeneficiary()->getId() == $this->getUser()->getParticular()->getAccount()->getId() ? 'beneficiary' : 'emiter';
        }

        return $transactionsByDate[$y]->getBeneficiary()->getId() == $this->getUser()->getCompany()->getAccount()->getId() ? 'beneficiary' : 'emiter';
    }

    public function dateToFrench($date, $format)
    {
        $english_days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $french_days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $english_months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $french_months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        return str_replace($english_months, $french_months, str_replace($english_days, $french_days, date($format, strtotime($date))));
    }
}
