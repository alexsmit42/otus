<?php

namespace App\Manager;

use App\Entity\Country;
use App\Entity\Currency;
use App\Entity\User;
use App\Enum\Direction;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class UserManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function createOrUpdate(string $login, Currency $currency, Country $country): User
    {
        if (!$user = $this->findByLogin($login)) {
            $user = new User();
            $user->setLogin($login);
            $user->setCurrency($currency); // TODO: recalculate balance
        }

        $user->setCountry($country);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function createFromAPI(string $login, int $countryId, int $currencyId): ?User
    {
        if (
            $this->findByLogin($login)
            || (!$country = $this->entityManager->getRepository(Country::class)->find($countryId))
            || (!$currency = $this->entityManager->getRepository(Currency::class)->find($currencyId))
        ) {
            return null;
        }

        $user = new User();
        $user->setLogin($login);
        $user->setCurrency($currency);
        $user->setCountry($country);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function update(int $id, int $countryId): bool
    {
        $user    = $this->entityManager->getRepository(User::class)->find($id);
        $country = $this->entityManager->getRepository(Country::class)->find($countryId);

        if (!$user || !$country) {
            return false;
        }

        $user->setCountry($country);
        $this->entityManager->flush();

        return true;
    }

    public function delete(User $user): bool
    {
        try {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        } catch (Exception) {
            // TODO: log/message error
            return false;
        }

        return true;
    }

    public function getAll(): array
    {
        return $this->entityManager->getRepository(User::class)->findAll();
    }

    public function findByLogin(string $login): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['login' => $login]);
    }

    public function findAvailableMethodsForDeposits(User $user): array
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        return $userRepository->findAvailableMethodsForDeposit($user);
    }

    public function findAvailableMethodsForWithdraw(User $user): array
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        return $userRepository->findAvailableMethodsForWithdraw($user);
    }
}