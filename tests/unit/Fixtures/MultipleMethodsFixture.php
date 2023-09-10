<?php

namespace UnitTests\Fixtures;

use App\Entity\Country;
use App\Entity\Method;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MultipleMethodsFixture extends Fixture
{
    public const BEELINE = 'Beeline';
    public const MIR     = 'Mir';
    public const VISA    = 'Visa';
    public const SOFORT  = 'Sofort';

    public function load(ObjectManager $manager)
    {
        $russia  = $this->getReference(MultipleCountriesFixture::RUSSIA);
        $germany = $this->getReference(MultipleCountriesFixture::GERMANY);

        $this->addReference(self::BEELINE, $this->makeMethod($manager, self::BEELINE, [$russia]));
        $this->addReference(self::MIR, $this->makeMethod($manager, self::MIR, [$russia]));
        $this->addReference(self::VISA, $this->makeMethod($manager, self::VISA, [$russia, $germany], 1000));
        $this->addReference(self::SOFORT, $this->makeMethod($manager, self::SOFORT, [$germany], 1000));

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @param string        $name
     * @param Country[]     $countries
     * @return Method
     */
    private function makeMethod(ObjectManager $manager, string $name, array $countries = [], float $minLimit = 100): Method
    {
        $method = new Method();
        $method->setName($name);
        $method->setMinLimit($minLimit);

        foreach ($countries as $country) {
            $method->addCountry($country);
        }

        $manager->persist($method);

        return $method;
    }
}