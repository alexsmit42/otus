<?php

namespace UnitTests\Fixtures;

use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MultipleCountriesFixture extends Fixture
{
    public const RUSSIA = 'Russia';
    public const GERMANY = 'Germany';

    public function load(ObjectManager $manager)
    {
        $this->addReference(self::RUSSIA, $this->makeCountry($manager, self::RUSSIA));
        $this->addReference(self::GERMANY, $this->makeCountry($manager, self::GERMANY));

        $manager->flush();
    }

    private function makeCountry(ObjectManager $manager, string $name): Country {
        $country = new Country();
        $country->setName($name);

        $manager->persist($country);

        return $country;
    }
}