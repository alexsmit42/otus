<?php

namespace App\Manager;

use App\DTO\Request\ManageCountryDTO;
use App\Entity\Country;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class CountryManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function createFromDTO(ManageCountryDTO $dto): Country
    {
        if (!$country = $this->findByName($dto->name)) {
            $country = new Country();
            $country->setName($dto->name);
            $this->entityManager->persist($country);
            $this->entityManager->flush();
        }

        return $country;
    }

    public function delete(Country $country): bool
    {
        $this->entityManager->flush();
        $this->entityManager->remove($country);

        return true;
    }

    public function findByName(string $name): ?Country
    {
        $name = ucfirst(strtolower($name));

        return $this->entityManager->getRepository(Country::class)->findOneBy(['name' => $name]);
    }

    public function getAll(): array
    {
        return $this->entityManager->getRepository(Country::class)->findAll();
    }
}