<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Enum\Direction;
use App\Enum\Status;
use App\Manager\MethodManager;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class TransactionService
{

    public function __construct(
        private readonly MethodManager $methodManager,
        private readonly UserBalanceService $balanceService,
    ) {
    }

    /**
     * Check if we can update transaction status
     * @param Transaction $transaction
     * @param Status      $status
     * @return bool
     */
    public function isAllowedToChangeStatus(Transaction $transaction, Status $status): bool
    {
        if ($transaction->getStatus() === $status) {
            throw new UnprocessableEntityHttpException('Status is same');
        }

        $isDepositFromFailToSuccess = (
            $transaction->getDirection() === Direction::DEPOSIT
            && $transaction->getStatus() === Status::FAIL
            && $status === Status::SUCCESS
        );

        // if final status, allow change only deposits from fail to success
        if (
            $transaction->getStatus()->isFinal()
            && !$isDepositFromFailToSuccess
        ) {
            throw new UnprocessableEntityHttpException('Transaction already has a final status');
        }

        return true;
    }

    /**
     * Check we can create transaction (allowed method for user, sufficient balance)
     * @param Transaction $transaction
     * @return bool
     */
    public function isAllowedTransactionCreate(Transaction $transaction): bool
    {
        if (!$this->methodManager->isAllowedForUser($transaction->getMethod(), $transaction->getPayer())) {
            throw new UnprocessableEntityHttpException("Method {$transaction->getMethod()->getName()} is not allowed for user");
        }

        if (
            $transaction->getDirection() === Direction::WITHDRAW
            && !$this->balanceService->isBalanceSufficient(
                $transaction->getPayer(),
                $transaction->getAmount(),
                $transaction->getCurrency()
            )
        ) {
            throw new UnprocessableEntityHttpException('Insufficient balance');
        }

        return true;
    }
}