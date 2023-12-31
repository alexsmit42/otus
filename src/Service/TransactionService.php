<?php

namespace App\Service;

use App\DTO\Message\TicketMessageDTO;
use App\DTO\Request\GetTransactionsFilterDTO;
use App\DTO\Request\ManageTransactionDTO;
use App\Entity\Transaction;
use App\Enum\Direction;
use App\Enum\Status;
use App\Manager\MethodManager;
use App\Manager\TransactionManager;
use App\Manager\UserManager;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class TransactionService
{
    public function __construct(
        private readonly MethodManager $methodManager,
        private readonly UserManager $userManager,
        private readonly TransactionManager $transactionManager,
        private readonly UserBalanceService $balanceService,
        private readonly ExchangeService $exchangeService,
        private readonly AsyncService $asyncService,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    public function getTransactions(GetTransactionsFilterDTO $dto): array
    {
        $method = null;
        if ($dto->method) {
            $method = $this->methodManager->getById($dto->method);
            if ($method === null) {
                return [];
            }
        }

        $user = null;
        if ($dto->payer) {
            $user = $this->userManager->getById($dto->payer);
            if ($user === null) {
                return [];
            }
        }

        // access only for ROLE_SUPPORT or for transactions where user is owner
        if (!$this->authorizationChecker->isGranted('get_transaction', $user)) {
            throw new AuthenticationException('Access denied');
        }

        return $this->transactionManager->getTransactions($user, $method, $dto->direction, $dto->status);
    }

    public function createFromDTO(ManageTransactionDTO $dto): bool
    {
        $transaction = $this->transactionManager->createFromDTO($dto);

        if (!$this->isAllowedTransactionCreate($transaction)) {
            return false;
        }

        $userAmount = $this->exchangeService->convertAmount(
            $transaction->getAmount(),
            $transaction->getCurrency(),
            $transaction->getPayer()->getCurrency()
        );

        $transaction->setUserAmount($userAmount);

        $this->transactionManager->save($transaction);

        $this->balanceService->updateBalance($transaction);
        // tickets queue
        $ticketMessage = (new TicketMessageDTO($transaction->getId()))->toAMQPMessage();
        $this->asyncService->publishToExchange(AsyncService::ADD_TICKET, $ticketMessage);

        return true;
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
            throw new UnprocessableEntityHttpException('Insufficient balance');
        }

        $oldStatus = $transaction->getStatus();

        $transaction->setStatus($status);

        $this->transactionManager->save($transaction);

        $this->balanceService->updateBalance($transaction, $oldStatus);

        return true;
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