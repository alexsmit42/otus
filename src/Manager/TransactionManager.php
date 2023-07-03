<?php

namespace App\Manager;

use App\DTO\ManageTransactionDTO;
use App\Entity\Transaction;
use App\Enum\Direction;
use App\Enum\Status;
use Doctrine\ORM\EntityManagerInterface;

class TransactionManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MethodManager $methodManager
    ) {
    }

    public function createFromDTO(ManageTransactionDTO $dto): ?Transaction
    {
        $transaction = new Transaction();

        if (!$this->methodManager->isAllowedForUser($dto->method, $dto->payer)) {
            return null;
        }

        $transaction->setAmount($dto->amount);
        $transaction->setCurrency($dto->currency);
        $transaction->setDirection($dto->direction);
        $transaction->setPayer($dto->payer);
        $transaction->setMethod($dto->method);
        $transaction->setPaymentDetails($dto->paymentDetails);

        $this->entityManager->persist($transaction);
        $this->entityManager->flush($transaction);

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
        if (!$this->isAllowedToChangeStatus($transaction, $status)) {
            return false;
        }

        $transaction->setStatus($status);

        $this->entityManager->flush();

        return true;
    }

    private function isAllowedToChangeStatus(Transaction $transaction, Status $status): bool
    {
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
}