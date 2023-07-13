<?php

namespace App\Manager;

use App\DTO\Request\ManageTransactionDTO;
use App\Entity\Currency;
use App\Entity\Method;
use App\Entity\Transaction;
use App\Entity\User;
use App\Enum\Direction;
use App\Enum\Status;
use App\Service\ExchangeService;
use App\Service\TransactionService;
use App\Service\UserBalanceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class TransactionManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TransactionService $transactionService,
        private readonly UserBalanceService $balanceService,
        private readonly ExchangeService $exchangeService,
    ) {
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
        $transaction->setPaymentDetails($dto->paymentDetails);

        $userAmount = $this->exchangeService->convertAmount(
            $transaction->getAmount(),
            $transaction->getCurrency(),
            $transaction->getPayer()->getCurrency()
        );

        $transaction->setUserAmount($userAmount);

        if (!$this->transactionService->isAllowedTransactionCreate($transaction)) {
            return null;
        }

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        $this->balanceService->updateBalance($transaction);

        return $transaction;
    }

    public function getById(int $id): Transaction
    {
        return $this->entityManager->getRepository(Transaction::class)->find($id);
    }

    public function updateStatus(Transaction $transaction, Status $status): bool
    {
        // if final status or same
        if (!$this->transactionService->isAllowedToChangeStatus($transaction, $status)) {
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
            throw new UnprocessableEntityHttpException('Insufficient balance');
        }

        $oldStatus = $transaction->getStatus();

        $transaction->setStatus($status);

        $this->entityManager->flush();

        $this->balanceService->updateBalance($transaction, $oldStatus);

        return true;
    }
}