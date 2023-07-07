<?php

namespace App\DataFixtures;

use App\Entity\Country;
use App\Entity\Method;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MethodFixtures extends Fixture
{
    /**
     * @var Country[] array
     */
    private array $countries = [];

    private array $minLimits = [null, 50, 100, 200, 500];
    private array $maxLimits = [null, 10000, 20000, 50000];

    public function load(ObjectManager $manager): void
    {
        $this->countries = $manager->getRepository(Country::class)->findAll();

        foreach ($this->getMethods() as $name) {
            $method = new Method();
            $method->setName($name[0]);

            if ($name[1] === 'all') {
                foreach ($this->countries as $country) {
                    $method->addCountry($country);
                }
            } else {
                foreach ($name[1] as $countryName) {
                    if ($country = $this->getCountryByName($countryName)) {
                        $method->addCountry($country);
                    }
                }
            }

            $method->setMinLimit($this->minLimits[array_rand($this->minLimits)]);
            $method->setMaxLimit($this->maxLimits[array_rand($this->maxLimits)]);

            $manager->persist($method);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CountryFixtures::class,
        ];
    }

    private function getMethods(): array
    {
        return [
            ['Visa', 'all'],
            ['Mastercard', 'all'],
            ['Beeline', ['Russia']],
            ['MTS', ['Russia']],
            ['Megafon', ['Russia']],
            ['Paypal', ['Germany', 'Poland', 'Sweden']],
            ['Dinabank', ['Serbia']],
            ['Western Union', ['Germany', 'Poland', 'Sweden', 'Serbia', 'Turkey']],
            ['Papara', ['Turkey']],
        ];
    }

    private function getCountryByName(string $name): ?Country
    {
        foreach ($this->countries as $country) {
            if ($country->getName() === $name) {
                return $country;
            }
        }

        return null;
    }
}
