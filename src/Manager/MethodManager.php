<?php

namespace App\Manager;

use App\Entity\Country;
use App\Entity\Method;
use App\Enum\Status;
use App\Repository\MethodRepository;
use Doctrine\ORM\EntityManagerInterface;

class MethodManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function create(string $name): Method
    {
        if (!$method = $this->findByName($name)) {
            $method = new Method();
            $method->setName($name);
            $this->entityManager->persist($method);
            $this->entityManager->flush();
        }

        return $method;
    }

    public function addCountry(Method $method, Country $country): void {
        $method->addCountry($country);

        $this->entityManager->flush();
    }

    public function removeCountry(Method $method, Country $country): void {
        $method->removeCountry($country);

        $this->entityManager->flush();
    }

    public function findByName(string $name): ?Method
    {
        return $this->entityManager->getRepository(Method::class)->findOneBy(['name' => $name]);
    }

    public function findSuccessfulTransactions(Method $method): array {
        /** @var MethodRepository $methodRepository */
        $methodRepository = $this->entityManager->getRepository(Method::class);

        return $methodRepository->findTransactionsByStatus($method, Status::SUCCESS);
    }

    public function findPendingTransactions(Method $method): array {
        /** @var MethodRepository $methodRepository */
        $methodRepository = $this->entityManager->getRepository(Method::class);

        return $methodRepository->findTransactionsByStatus($method, Status::PENDING);
    }
}