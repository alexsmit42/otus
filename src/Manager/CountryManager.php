<?php

namespace App\Manager;

use App\Entity\Country;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;

class CountryManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function create(string $name): Country
    {
        if (!$country = $this->findByName($name)) {
            $country = new Country();
            $country->setName($name);
            $this->entityManager->persist($country);
            $this->entityManager->flush();
        }

        return $country;
    }

    public function findByName(string $name): ?Country {
        return $this->entityManager->getRepository(Country::class)->findOneBy(['name' => $name]);
    }

    public function getCountMethodsByCountry(): array {
        /** @var CountryRepository $countryRepository */
        $countryRepository = $this->entityManager->getRepository(Country::class);

        return $countryRepository->getCountMethodsByCountry();
    }
}