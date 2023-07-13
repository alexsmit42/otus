<?php

namespace App\Manager;

use App\DTO\Request\ManageMethodDTO;
use App\Entity\Country;
use App\Entity\Method;
use App\Entity\User;
use App\Enum\Status;
use App\Repository\MethodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Throwable;

class MethodManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    private function save(Method $method): bool
    {
        if (
            $method->getMinLimit()
            && $method->getMaxLimit()
            && $method->getMinLimit() >= $method->getMaxLimit()
        ) {
            throw new UnprocessableEntityHttpException('minLimit should be less then maxLimit');
        }

        $this->entityManager->persist($method);
        $this->entityManager->flush();

        return true;
    }

    public function createFromDTO(ManageMethodDTO $dto): Method
    {
        if (!$method = $this->findByName($dto->name)) {
            $method = new Method();
            $method->setName($dto->name);
            $method->setMinLimit($dto->min_limit);
            $method->setMaxLimit($dto->max_limit);

            $this->save($method);
        }

        return $method;
    }

    public function updateFromDTO(int $id, ManageMethodDTO $dto): bool
    {
        $method = $this->entityManager->getRepository(Method::class)->find($id);

        if ($method === null) {
            throw new UnprocessableEntityHttpException('Method does not exists');
        }

        if ($dto->min_limit) {
            $method->setMinLimit($dto->min_limit);
        }

        if ($dto->max_limit) {
            $method->setMaxLimit($dto->max_limit);
        }

        return $this->save($method);
    }

    public function delete(Method $method): bool
    {
        $this->entityManager->remove($method);
        $this->entityManager->flush();

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

    public function isAllowedForUser(Method $method, User $user): bool
    {
        return $method->getCountries()->contains($user->getCountry());
    }

    public function findTransactionsByStatus(
        Method $method,
        ?Status $status = null
    ) {
        /** @var MethodRepository $methodRepository */
        $methodRepository = $this->entityManager->getRepository(Method::class);

        return $methodRepository->findTransactionsByStatus($method, $status);
    }

    public function countByCountry(Country $country): int
    {
        /** @var MethodRepository $methodRepository */
        $methodRepository = $this->entityManager->getRepository(Method::class);

        return $methodRepository->countMethodsByCountry($country);
    }
}