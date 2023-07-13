<?php

namespace App\Manager;

use App\Entity\Country;
use App\Entity\Currency;
use App\Entity\User;
use App\Enum\Direction;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class UserManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function createFromRequest(Request $request): ?User
    {
        $login      = $request->request->get('login');
        $countryId  = $request->request->get('country_id');
        $currencyId = $request->request->get('currency_id');

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

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function update(int $id, int $countryId): bool
    {
        $user    = $this->entityManager->getRepository(User::class)->find($id);
        $country = $this->entityManager->getRepository(Country::class)->find($countryId);

        if (!$user || !$country) {
            throw new UnprocessableEntityHttpException('User or country do not exist');
        }

        $user->setCountry($country);
        $this->entityManager->flush();

        return true;
    }

    public function delete(User $user): bool
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

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