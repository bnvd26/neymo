<?php

namespace App\Controller\API;

use App\Entity\Transaction;
use App\Repository\AccountRepository;
use App\Repository\TransactionRepository;
use App\Repository\CurrencyRepository;
use App\Services\CreditCardService;
use App\Services\CurrencyService;
use Exception;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\API\ApiController;
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

        if ((int) $accountRepository->find($data->emiterAccountId)->getAvailableCash() < (int) $data->transferedMoney ||
            (int) $data->transferedMoney < 0
        ) {
            $response = new Response();
            $response->setStatusCode(Response::HTTP_OK);
            $response->setContent(json_encode([
                'Error' => "Vous n'avez pas les fonds necéssaires pour transférer de l'argent",
            ]));
            $response->headers->set('Content-Type', 'application/json');

            return $response;

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

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($transaction);
        $entityManager->flush();
        $response = new Response();
        $response->setStatusCode(Response::HTTP_CREATED);
        $response->setContent(json_encode([
            'Success' => "Argent bien envoyé",
        ]));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("api/convertToEuro", name="api_converToEuro", methods="POST")
     *
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
     * @SWG\Parameter(
     *     name="emiterAccountId",
     *     in="query",
     *     type="number",
     *     description="The emitter account of the transaction"
     * )
     * @SWG\Tag(name="transaction")
     *
     * @param CurrencyService $currencyService
     * @param Request $request
     * @param CurrencyRepository $currencyRepository
     * @param AccountRepository $accountRepository
     * @param CreditCardService $creditCardService
     *
     * @throws Exception
     *
     * @return JsonResponse
     */
    public function convertToEuro(
        CurrencyService $currencyService,
        Request $request,
        CurrencyRepository $currencyRepository,
        AccountRepository $accountRepository,
        CreditCardService $creditCardService
    ): JsonResponse {
        $value = (float) $request->get("value");
        $currencyId = (int) $request->get('currency');
        $currency = $currencyRepository->find($currencyId);
        $cardNumber = $request->get("card-number");
        $cardType = $request->get("card-type");
        $cvc = $request->get("cvc");
        $month = $request->get("month");
        $year = $request->get("year");
        $cardHolderName = $request->get("card-holder-name");
        $emiterAccountId = $request->get("emiterAccountId");
        $convertedValue = $currencyService->convertToEuro(
            $currency->getExchangeRate(),
            $value
        );
        if ($this->getUser()->isParticular()) {
            return new JsonResponse([
                "status" => "error",
                "error" => "User not authorized"
            ], Response::HTTP_NOT_ACCEPTABLE);
        }
        if (false === $creditCardService->checkItAll(
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
        }
        $transaction = new Transaction();
        $emiterAccount = $accountRepository->find($emiterAccountId);
        if (null === $emiterAccount) {
            return new JsonResponse([
                "status" => "error",
                "error" => "Accounts information is invalid"
            ], Response::HTTP_NOT_FOUND);
        }
        if ($value > $emiterAccount->getAvailableCash()) {
            return new JsonResponse([
                "status" => "error",
                "error" => "Not enough cash"
            ], Response::HTTP_NOT_ACCEPTABLE);
        }
        $transaction->setEmiter($emiterAccount);
        $accountRepository->find($emiterAccountId)->removeMoneyToEmiter($value);
        $transaction->setTransferedMoney($value);
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

    public function getTransactionsForParticular(TransactionRepository $transactionRepository)
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
