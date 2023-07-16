<?php

namespace App\Manager;

use App\DTO\Request\ManageTransactionDTO;
use App\Entity\Currency;
use App\Entity\Method;
use App\Entity\Transaction;
use App\Entity\User;
use App\Enum\Direction;
use App\Enum\Status;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class TransactionManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function save(Transaction $transaction): void {
        $this->entityManager->persist($transaction);
        $this->entityManager->flush();
    }

    public function createFromDTO(ManageTransactionDTO $dto): ?Transaction
    {
        $currency = $this->entityManager->getRepository(Currency::class)->find($dto->currency);
        if (!$currency) {
            throw new UnprocessableEntityHttpException('Currency does not exists');
        }

        $payer = $this->entityManager->getRepository(User::class)->find($dto->payer);
        if (!$payer) {
            throw new UnprocessableEntityHttpException('Payer does not exists');
        }

        $method = $this->entityManager->getRepository(Method::class)->find($dto->method);
        if (!$method) {
            throw new UnprocessableEntityHttpException('Method does not exists');
        }

        $transaction = new Transaction();

        $transaction->setAmount($dto->amount);
        $transaction->setCurrency($currency);
        $transaction->setDirection($dto->direction);
        $transaction->setPayer($payer);
        $transaction->setMethod($method);
        $transaction->setPaymentDetails($dto->payment_details);

        return $transaction;
    }

    public function getById(int $id): Transaction
    {
        return $this->entityManager->getRepository(Transaction::class)->find($id);
    }

    public function getTransactions(?User $user, ?Method $method, ?Direction $direction = null, ?Status $status = null): array
    {
        /** @var TransactionRepository $transactionRepository */
        $transactionRepository = $this->entityManager->getRepository(Transaction::class);

        return $transactionRepository->getTransactions($user, $method, $direction, $status);
    }
}