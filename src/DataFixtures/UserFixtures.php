<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Manager\CountryManager;
use App\Manager\CurrencyManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private readonly CountryManager $countryManager,
        private readonly CurrencyManager $currencyManager
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getUsers() as $item) {
            $user = new User();
            $user->setLogin($item[0]);
            $user->setCurrency($this->currencyManager->findByIso($item[1]));
            $user->setCountry($this->countryManager->findByName($item[2]));
            $manager->persist($user);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CountryFixtures::class,
            CurrencyFixtures::class,
        ];
    }

    private function getUsers(): array {
        return [
            ['Alex', 'RUB', 'Russia'],
            ['Tomas', 'EUR', 'Germany'],
            ['Grzegorz', 'PLN', 'Poland'],
            ['Olaf', 'USD', 'Sweden'],
            ['Ville', 'EUR', 'Finland'],
            ['Bojan', 'RSD', 'Serbia'],
            ['Ahmet', 'TRY', 'Turkey'],
            ['Frunzik', 'AMD', 'Armenia'],
        ];
    }

    public static function getGroups(): array
    {
        return ['users'];
    }
}
