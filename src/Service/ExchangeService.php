<?php

namespace App\Service;

use App\Entity\Currency;

class ExchangeService
{
    public function convertAmount(float $amount, Currency $fromCurrency, Currency $toCurrency): float
    {
        $ratio = $fromCurrency->getRate() / $toCurrency->getRate();

        return $amount * $ratio;
    }

    public function convertAmountToBaseCurrency(float $amount, Currency $currency): float
    {
        return $amount * $currency->getRate();
    }
}