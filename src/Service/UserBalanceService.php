<?php

namespace App\Service;

use App\Entity\Currency;
use App\Entity\Transaction;
use App\Entity\User;
use App\Enum\Direction;
use App\Enum\Status;
use Doctrine\ORM\EntityManagerInterface;

class UserBalanceService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ExchangeService $exchangeService,
    ) {
    }

    public function updateBalance(Transaction $transaction): bool
    {
        $newBalance = null;

        if (
            $transaction->getDirection() === Direction::DEPOSIT
            && $transaction->getStatus() === Status::SUCCESS
        ) {
            $newBalance = $this->upBalance($transaction->getPayer(), $transaction->getAmount(), $transaction->getCurrency());
        }

        if (
            $transaction->getDirection() === Direction::WITHDRAW
            && $transaction->getStatus() === Status::FAIL
        ) {
            $newBalance = $this->downBalance($transaction->getPayer(), $transaction->getAmount(), $transaction->getCurrency());
        }

        if ($newBalance) {
            // TODO: BalanceHistory

            //$this->entityManager->flush();

            return true;
        }

        return false;
    }

    private function upBalance(User $user, float $amount, Currency $currency): ?float
    {
        $userAmount = $this->exchangeService->convertAmount($amount, $currency, $user->getCurrency());

        $newBalance = $user->getBalance() + $userAmount;
        $user->setBalance($newBalance);
        $this->entityManager->flush();

        return $newBalance;
    }

    private function downBalance(User $user, float $amount, Currency $currency): ?float
    {
        $userAmount = $this->exchangeService->convertAmount($amount, $currency, $user->getCurrency());

        if ($user->getBalance() < $userAmount) {
            return false;
        }

        $newBalance = $user->getBalance() - $userAmount;
        $user->setBalance($newBalance);
        $this->entityManager->flush();

        return $newBalance;
    }
}