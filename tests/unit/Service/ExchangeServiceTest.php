<?php

namespace UnitTests\Service;

use App\Entity\Currency;
use App\Service\ExchangeService;
use PHPUnit\Framework\TestCase;

class ExchangeServiceTest extends TestCase
{
    public function convertAmountDataProvider(): array
    {
        $currencyRUB = (new Currency())
            ->setRate(1);

        $currencyEUR = (new Currency())
            ->setRate(120);

        $currencyUSD = (new Currency())
            ->setRate(100);

        return [
            'rub_rub' => [
                100,
                $currencyRUB,
                $currencyRUB,
                100,
            ],
            'eur_rub' => [
                100,
                $currencyEUR,
                $currencyRUB,
                12000,
            ],
            'rub_usd' => [
                1000,
                $currencyRUB,
                $currencyUSD,
                10,
            ],
        ];
    }

    /**
     * @dataProvider convertAmountDataProvider
     */
    public function testConvertAmount(float $amount, Currency $fromCurrency, Currency $toCurrency, float $expected): void
    {
        static::assertSame($expected, (new ExchangeService())->convertAmount($amount, $fromCurrency, $toCurrency));
    }

    public function convertToBaseDataProvider(): array
    {
        $currencyRUB = (new Currency())
            ->setRate(1);

        $currencyEUR = (new Currency())
            ->setRate(120);

        $currencyUSD = (new Currency())
            ->setRate(100);

        return [
            'rub' => [
                100,
                $currencyRUB,
                100,
            ],
            'eur' => [
                100,
                $currencyEUR,
                12000,
            ],
            'usd' => [
                10,
                $currencyUSD,
                1000,
            ],
        ];
    }

    /**
     * @dataProvider convertToBaseDataProvider
     */
    public function testConvertToBase(float $amount, Currency $fromCurrency, float $expected): void
    {
        static::assertSame($expected, (new ExchangeService())->convertAmountToBaseCurrency($amount, $fromCurrency));
    }
}