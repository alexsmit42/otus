<?php

namespace UnitTests\Fixtures;

use App\Entity\Country;
use App\Entity\Currency;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MultipleUsersFixture extends Fixture
{
    public const RUB_100 = 'RUB_100';
    public const RUB_1000 = 'RUB_1000';
    public const USD_100 = 'USD_100';
    public const EUR_100 = 'EUR_100';

    public function load(ObjectManager $manager)
    {
        $rub = $this->getReference(MultipleCurrenciesFixture::RUB);
        $eur = $this->getReference(MultipleCurrenciesFixture::EUR);
        $usd = $this->getReference(MultipleCurrenciesFixture::USD);

        $russia = $this->getReference(MultipleCountriesFixture::RUSSIA);
        $germany = $this->getReference(MultipleCountriesFixture::GERMANY);

        $this->addReference(
            self::RUB_100,
            $this->makeUser($manager, self::RUB_100, $rub, 100, $russia)
        );

        $this->addReference(
            self::RUB_1000,
            $this->makeUser($manager, self::RUB_1000, $rub, 1000, $russia)
        );

        $this->addReference(
            self::USD_100,
            $this->makeUser($manager, self::USD_100, $usd, 100, $russia)
        );

        $this->addReference(
            self::EUR_100,
            $this->makeUser($manager, self::EUR_100, $eur, 100, $germany)
        );

        $manager->flush();
    }

    private function makeUser(ObjectManager $manager, string $login, Currency $currency, float $balance, Country $country): User {
        $user = new User();
        $user->setLogin($login);
        $user->setPassword("{$login}_123");
        $user->setCurrency($currency);
        $user->setBalance($balance);
        $user->setCountry($country);
        $user->setRoles([]);

        $manager->persist($user);

        return $user;
    }
}