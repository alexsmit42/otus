<?php

namespace UnitTests\Fixtures;

use App\Entity\Currency;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MultipleCurrenciesFixture extends Fixture
{
    public const RUB = 'RUB';
    public const EUR = 'EUR';
    public const USD = 'USD';

    public function load(ObjectManager $manager)
    {
        $this->addReference(self::RUB, $this->makeCurrency($manager, self::RUB, 1));
        $this->addReference(self::EUR, $this->makeCurrency($manager, self::EUR, 120));
        $this->addReference(self::USD, $this->makeCurrency($manager, self::USD, 100));

        $manager->flush();
    }

    private function makeCurrency(ObjectManager $manager, string $iso, float $rate): Currency {
        $currency = new Currency();
        $currency->setIso($iso);
        $currency->setRate($rate);

        $manager->persist($currency);

        return $currency;
    }
}