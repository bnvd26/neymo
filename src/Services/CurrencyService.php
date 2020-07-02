<?php

namespace App\Services;


use Exception;

/**
 * Class CurrencyService
 * @package App\Services
 */
class CurrencyService
{
    /**
     * Maximum exchange rate purcentage
     */
    private const RATE_MAXIMUM = 100;

    /**
     * Minimum exchange rate purcentage
     */
    private const RATE_MINIMUM = 90;

    private const PRECISION_DEFAULT = 2;

    /**
     * CurrencyService constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param int $exchangeRate
     * @param float $value
     * @param int $precision
     *
     * @throws Exception
     *
     * @return false|float
     */
    public function convertToEuro(int $exchangeRate, float $value, int $precision = self::PRECISION_DEFAULT)
    {
        $this->converterParamsValidator($exchangeRate, $value);
        if (0.0 === $value) {
            return 0.0;
        }
        return round(($value * $exchangeRate / 100), $precision);
    }

    /**
     * @param int $exchangeRate
     * @param float $value
     *
     * @throws Exception
     */
    private function converterParamsValidator(int $exchangeRate, float $value)
    {
        if (self::RATE_MINIMUM > $exchangeRate || self::RATE_MAXIMUM < $exchangeRate) {
            throw new Exception('Taux de change impossible');
        }
        if (0 > $value) {
            throw new Exception('La valeur doit être supérieure ou égale à 0');
        }
    }
}
