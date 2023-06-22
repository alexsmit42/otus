<?php

namespace App\Manager;

use App\Entity\Country;
use App\Entity\Currency;
use App\Entity\User;
use App\Enum\Direction;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function create(string $login, Currency $currency, Country $country): User
    {
        if (!$user = $this->findByLogin($login)) {
            $user = new User();
            $user->setLogin($login);
            $user->setCurrency($currency); // Currency can't be just changed
        }

        $user->setCountry($country);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function findByLogin(string $login): ?User {
        return $this->entityManager->getRepository(User::class)->findOneBy(['login' => $login]);
    }

    public function findAvailableMethods(User $user, Direction $direction = Direction::DEPOSIT): array {
        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        return $userRepository->findAvailableMethods($user, $direction);
    }
}