<?php

namespace App\Services;

use Inacho\CreditCard;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CreditCardService
 * @package App\Services
 */
class CreditCardService
{
    /**
     * CurrencyService constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param string $cardNumber
     * @param string $cardType
     * @param string $cvc
     * @param string $cardHolderName
     * @param string $year
     * @param string $month
     *
     * @return bool
     */
    public function checkItAll(
        string $cardNumber,
        string $cardType,
        string $cvc,
        string $cardHolderName,
        string $year,
        string $month
    ) {
        dump($this->validDate($year, $month));
        if (false === $this->validCreditCard($cardNumber, $cardType) ||
            false === $this->validCvc($cvc, $cardType) ||
            null === $cardHolderName ||
            false === $this->validDate($year, $month)
        ) {
            return false;
        }

        return true;
    }
    /**
     * @param int $cardNumber
     * @param string $cardType
     *
     * @return array
     */
    public function creditCard(int $cardNumber, string $cardType): array
    {
        return CreditCard::validCreditCard($cardNumber, $cardType);
    }

    /**
     * @param int $cardNumber
     * @param string $cardType
     *
     * @return bool
     */
    public function validCreditCard(int $cardNumber, string $cardType): bool
    {
        return current(CreditCard::validCreditCard($cardNumber, $cardType));
    }

    /**
     * @param string $cvc
     * @param string $cardType
     *
     * @return bool
     */
    public function validCvc(string $cvc, string $cardType): bool
    {
        return CreditCard::validCvc($cvc, $cardType);
    }

    /**
     * @param string $year
     * @param string $month
     *
     * @return bool
     */
    public function validDate(string $year, string $month)
    {
        return CreditCard::validDate($year, $month);
    }
}
