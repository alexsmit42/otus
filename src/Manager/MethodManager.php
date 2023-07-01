<?php

namespace App\Manager;

use App\Entity\Country;
use App\Entity\Method;
use App\Enum\Status;
use App\Repository\MethodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class MethodManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function createOrUpdate(
        string $name,
        ?float $minLimit = null,
        ?float $maxLimit = null,
    ): Method {
        if (!$method = $this->findByName($name)) {
            $method = new Method();
            $method->setName($name);
            $method->setMinLimit($minLimit);
            $method->setMaxLimit($maxLimit);
            $this->entityManager->persist($method);
            $this->entityManager->flush();
        }

        return $method;
    }

    public function update(int $id, ?float $minLimit, ?float $maxLimit): bool
    {
        $method = $this->entityManager->getRepository(Method::class)->find($id);

        if (!$method) {
            return false;
        }

        if ($minLimit) {
            $method->setMinLimit($minLimit);
        }
        if ($maxLimit) {
            $method->setMinLimit($maxLimit);
        }
        $this->entityManager->flush();

        return true;
    }

    public function delete(Method $method): bool
    {
        try {
            $this->entityManager->remove($method);
            $this->entityManager->flush();
        } catch (Exception) {
            // TODO: log/message error
            return false;
        }

        return true;
    }

    public function addCountry(Method $method, Country $country): bool
    {
        $method->addCountry($country);

        $this->entityManager->flush();

        return true;
    }

    public function removeCountry(Method $method, Country $country): bool
    {
        $method->removeCountry($country);

        $this->entityManager->flush();

        return true;
    }

    public function getAll(): array
    {
        return $this->entityManager->getRepository(Method::class)->findAll();
    }

    public function findByName(string $name): ?Method
    {
        return $this->entityManager->getRepository(Method::class)->findOneBy(['name' => $name]);
    }

    public function findSuccessfulTransactions(Method $method): array
    {
        /** @var MethodRepository $methodRepository */
        $methodRepository = $this->entityManager->getRepository(Method::class);

        return $methodRepository->findTransactionsByStatus($method, Status::SUCCESS);
    }

    public function findPendingTransactions(Method $method): array
    {
        /** @var MethodRepository $methodRepository */
        $methodRepository = $this->entityManager->getRepository(Method::class);

        return $methodRepository->findTransactionsByStatus($method, Status::PENDING);
    }

    public function countByCountry(Country $country): int
    {
        /** @var MethodRepository $methodRepository */
        $methodRepository = $this->entityManager->getRepository(Method::class);

        return $methodRepository->countMethodsByCountry($country);
    }
}