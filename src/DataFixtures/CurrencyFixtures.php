<?php

namespace App\DataFixtures;

use App\Entity\Currency;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CurrencyFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        foreach ($this->getCurrencies() as $item) {
            $currency = new Currency();
            $currency->setIso($item[0]);
            $currency->setRate($item[1]);
            $manager->persist($currency);
        }

        $manager->flush();
    }

    private function getCurrencies(): array {
        return [
            ['RUB', 1],
            ['USD', 91],
            ['EUR', 98],
            ['PLN', 19.5],
            ['TRY', 3.45],
            ['AMD', 0.23],
            ['RSD', 0.83],
        ];
    }
}
