<?php

namespace App\Manager;

use App\DTO\ManageTransactionDTO;
use App\Entity\Transaction;
use App\Enum\Direction;
use App\Enum\Status;
use App\Service\ExchangeService;
use App\Service\UserBalanceService;
use Doctrine\ORM\EntityManagerInterface;

class TransactionManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MethodManager $methodManager,
        private readonly UserBalanceService $balanceService,
        private readonly ExchangeService $exchangeService,
    ) {
    }

    public function createFromDTO(ManageTransactionDTO $dto): ?Transaction
    {
        $transaction = new Transaction();

        $transaction->setAmount($dto->amount);
        $transaction->setCurrency($dto->currency);
        $transaction->setDirection($dto->direction);
        $transaction->setPayer($dto->payer);
        $transaction->setMethod($dto->method);
        $transaction->setPaymentDetails($dto->paymentDetails);

        $userAmount = $this->exchangeService->convertAmount(
            $transaction->getAmount(),
            $transaction->getCurrency(),
            $transaction->getPayer()->getCurrency()
        );

        $transaction->setUserAmount($userAmount);

        if (!$this->isAllowedTransactionCreate($transaction)) {
            return null;
        }

        $this->entityManager->persist($transaction);
        $this->entityManager->flush($transaction);

        $this->balanceService->updateBalance($transaction);

        return $transaction;
    }

    public function updateFromDTO(Transaction $transaction, ManageTransactionDTO $dto): ?Transaction
    {
        if (!$this->isAllowedToChangeStatus($transaction, $dto->status)) {
            return null;
        }

        if (!$this->methodManager->isAllowedForUser($dto->method, $transaction->getPayer())) {
            return null;
        }

        $transaction->setStatus($dto->status);
        $transaction->setPaymentDetails($dto->paymentDetails);
        $transaction->setMethod($dto->method);

        $this->entityManager->flush($transaction);

        return $transaction;
    }

    public function getById(int $id): Transaction
    {
        return $this->entityManager->getRepository(Transaction::class)->find($id);
    }

    public function updateStatus(Transaction $transaction, Status $status): bool
    {
        // if final status or same
        if (!$this->isAllowedToChangeStatus($transaction, $status)) {
            return false;
        }

        // is enough balance for withdraw
        if (
            $transaction->getDirection() === Direction::WITHDRAW
            && !$this->balanceService->isBalanceSufficient(
                $transaction->getPayer(),
                $transaction->getAmount(),
                $transaction->getCurrency(),
            )
        ) {
            return false;
        }

        $oldStatus = $transaction->getStatus();

        $transaction->setStatus($status);

        $this->entityManager->flush();

        $this->balanceService->updateBalance($transaction, $oldStatus);

        return true;
    }

    private function isAllowedToChangeStatus(Transaction $transaction, Status $status): bool
    {
        if ($transaction->getStatus() === $status) {
            return false;
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
            return false;
        }

        return true;
    }

    private function isAllowedTransactionCreate(Transaction $transaction): bool
    {
        if (!$this->methodManager->isAllowedForUser($transaction->getMethod(), $transaction->getPayer())) {
            return false;
        }

        if (
            $transaction->getDirection() === Direction::WITHDRAW
            && !$this->balanceService->isBalanceSufficient(
                $transaction->getPayer(),
                $transaction->getAmount(),
                $transaction->getCurrency()
            )
        ) {
            return false;
        }

        return true;
    }
}