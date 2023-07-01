<?php

namespace App\Manager;

use App\Entity\Country;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class CountryManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function createOrUpdate(string $name): Country
    {
        if (!$country = $this->findByName($name)) {
            $country = new Country();
            $country->setName($name);
            $this->entityManager->persist($country);
            $this->entityManager->flush();
        }

        return $country;
    }

    public function delete(Country $country): bool {
        try {
            $this->entityManager->remove($country);
            $this->entityManager->flush();
        } catch (Exception) {
            // TODO: log/message error
            return false;
        }

        return true;
    }

    public function findByName(string $name): ?Country {
        $name = ucfirst(strtolower($name));

        return $this->entityManager->getRepository(Country::class)->findOneBy(['name' => $name]);
    }

    public function getAll(): array {
        /** @var CountryRepository $countryRepository */
        $countryRepository = $this->entityManager->getRepository(Country::class);

        return $countryRepository->findAll();
    }

    public function getCountMethodsByCountry(): array {
        /** @var CountryRepository $countryRepository */
        $countryRepository = $this->entityManager->getRepository(Country::class);

        return $countryRepository->getCountMethodsByCountry();
    }
}