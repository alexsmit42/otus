<?php

namespace App\Manager;

use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;

class TransactionManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MethodManager $methodManager
    ) {
    }

    public function save(Transaction $transaction): ?Transaction
    {
        if (!$this->methodManager->isAllowedForUser($transaction->getMethod(), $transaction->getPayer())) {
            return null;
        }

        $this->entityManager->flush($transaction);

        return $transaction;
    }

    public function getById(int $id): Transaction
    {
        return $this->entityManager->getRepository(Transaction::class)->find($id);
    }
}