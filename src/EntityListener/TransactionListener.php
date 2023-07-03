<?php

namespace App\EntityListener;

use App\Entity\Transaction;
use App\Enum\Direction;
use App\Enum\Status;
use App\Service\UserBalanceService;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class TransactionListener
{
    public function __construct(private readonly UserBalanceService $balanceService)
    {
    }

    // is update balance needed?
    public function preUpdate(Transaction $transaction, PreUpdateEventArgs $event): void
    {
        $oldStatus = Status::from($event->getOldValue('status'));
        $newStatus = Status::from($event->getNewValue('status'));

        if (!$newStatus->isFinal() || $oldStatus === $newStatus) {
            return;
        }

        $isDepositUpdate = (
            $transaction->getDirection() === Direction::DEPOSIT
            && $newStatus === Status::SUCCESS
        );

        $isWithdrawUpdate = (
            $transaction->getDirection() === Direction::WITHDRAW
            && $newStatus === Status::FAIL
        );

        if ($isDepositUpdate || $isWithdrawUpdate) {
            $this->balanceService->updateBalance($transaction);
        }
    }
}