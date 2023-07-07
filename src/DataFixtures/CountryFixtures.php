<?php

namespace App\DataFixtures;

use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CountryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        foreach ($this->getCountries() as $name) {
            $country = new Country();
            $country->setName($name);
            $manager->persist($country);
        }

        $manager->flush();
    }

    private function getCountries(): array {
        return [
            'Russia',
            'Germany',
            'Poland',
            'Sweden',
            'Finland',
            'Serbia',
            'Turkey',
            'Armenia',
        ];
    }
}
