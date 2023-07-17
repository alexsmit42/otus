<?php

namespace App\Manager;

use App\DTO\Request\ManageUserDTO;
use App\Entity\Country;
use App\Entity\Currency;
use App\Entity\User;
use App\Enum\Direction;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    private function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function createFromDTO(ManageUserDTO $dto): ?User
    {
        $login      = $dto->login;
        $countryId  = $dto->country;
        $currencyId = $dto->currency;

        if ($this->findByLogin($login)) {
            throw new UnprocessableEntityHttpException('Incorrect login');
        }

        $country  = $this->entityManager->getRepository(Country::class)->find($countryId);
        $currency = $this->entityManager->getRepository(Currency::class)->find($currencyId);

        if (!$country || !$currency) {
            throw new UnprocessableEntityHttpException('Currency or country do not exist');
        }

        $user = new User();
        $user->setLogin($login);
        $user->setCurrency($currency);
        $user->setCountry($country);
        $user->setRoles($dto->roles);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $dto->password));

        $this->save($user);

        return $user;
    }

    public function updateFromDTO(User $user, ManageUserDTO $dto): bool
    {
        $country = $this->entityManager->getRepository(Country::class)->find($dto->country);

        if ($country === null) {
            throw new UnprocessableEntityHttpException('Country does not exist');
        }

        $user->setCountry($country);
        $user->setRoles($dto->roles);

        $this->save($user);

        return true;
    }

    public function updatePassword(User $user, string $password): bool
    {
        $newHash = $this->userPasswordHasher->hashPassword($user, $password);

        if ($user->getPassword() === $newHash) {
            throw new UnprocessableEntityHttpException('This password is already in use');
        }

        $user->setPassword($newHash);

        $this->save($user);

        return true;
    }

    public function delete(User $user): bool
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return true;
    }

    public function getById(int $id): ?User
    {
        return $this->entityManager->getRepository(User::class)->find($id);
    }

    public function getAll(): array
    {
        return $this->entityManager->getRepository(User::class)->findAll();
    }

    public function findByLogin(string $login): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['login' => $login]);
    }

    public function findAvailableMethods(User $user, Direction $direction = Direction::DEPOSIT): array
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        if ($direction === Direction::WITHDRAW) {
            return $userRepository->findAvailableMethodsForWithdraw($user);
        }

        return $userRepository->findAvailableMethodsForDeposit($user);
    }
}