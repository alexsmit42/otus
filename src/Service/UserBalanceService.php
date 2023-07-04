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

    public function updateBalance(Transaction $transaction, ?Status $oldStatus = null): bool
    {
        $newBalance = null;

        $newStatus = $transaction->getStatus();

        if ($oldStatus === $newStatus) {
            return false;
        }

        $isDepositUpdate = (
            $transaction->getDirection() === Direction::DEPOSIT
            && $newStatus === Status::SUCCESS
        );

        $isWithdrawUpdate = (
            $transaction->getDirection() === Direction::WITHDRAW
            && ($newStatus === Status::FAIL || $newStatus === Status::NEW)
        );

        if ($isDepositUpdate) {
            $newBalance = $this->upBalance($transaction->getPayer(), $transaction->getAmount(), $transaction->getCurrency());
        } else if ($isWithdrawUpdate) {
            $newBalance = $this->downBalance($transaction->getPayer(), $transaction->getAmount(), $transaction->getCurrency());
        }

        if ($newBalance) {
            // TODO: BalanceHistory

            $this->entityManager->flush();
        }

        return true;
    }

    private function upBalance(User $user, float $amount, Currency $currency): float
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

        if ($this->isBalanceSufficient($user, $amount, $currency)) {
            return null;
        }

        $newBalance = $user->getBalance() - $userAmount;
        $user->setBalance($newBalance);
        $this->entityManager->flush();

        return $newBalance;
    }

    public function isBalanceSufficient(User $user, float $amount, Currency $currency): bool {
        $userAmount = $this->exchangeService->convertAmount($amount, $currency, $user->getCurrency());

        if ($user->getBalance() < $userAmount) {
            return false;
        }

        return true;
    }
}