<?php

namespace App\Tests;

use App\Services\CurrencyService;
use Exception;
use PHPUnit\Framework\TestCase;

class CurrencyServiceTest extends TestCase
{
    /**
     * @dataProvider convertProvider
     *
     * @param $exchangeRate
     * @param $value
     * @param $precision
     * @param $expected
     *
     * @throws Exception
     */
    public function testConvertToEuro($exchangeRate, $value, $precision, $expected)
    {
        $service = new CurrencyService();
        $this->assertEquals($expected, $service->convertToEuro($exchangeRate, $value, $precision));
    }

    /**
     * @
     */
    public function convertProvider()
    {
        return [
            [
                100, 100, 2, 100.0
            ],
            [
                95, 100, 2, 95.0
            ],
        ];
    }
}
