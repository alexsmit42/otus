<?php

namespace App\Service;

use App\DTO\Message\TicketMessageDTO;
use App\Entity\Ticket;
use App\Enum\Status;
use App\Manager\TicketManager;
use App\Manager\TransactionManager;
use App\Manager\UserManager;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class TicketService
{

    public function __construct(
        private readonly TransactionManager $transactionManager,
        private readonly TicketManager $ticketManager,
        private readonly UserManager $userManager,
        private readonly Security $security,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly LoggerInterface $logger,
    )
    {
    }

    /**
     * При получении из очереди порции новых транзакций, для них создаются сущности тикетов
     *
     * @param TicketMessageDTO $ticketMessageDTO
     * @return Ticket|null
     */
    public function createTicketFromDTO(TicketMessageDTO $ticketMessageDTO): ?Ticket {
        $transaction = $this->transactionManager->getById($ticketMessageDTO->getTransactionId());
        if ($transaction === null) {
            $this->logger->notice("Ticket for non existing transaction {$ticketMessageDTO->getTransactionId()}");
            return null;
        }

        // проверяем что для данной транзакции тикетов еще не было
        if ($this->ticketManager->findByTransaction($transaction)) {
            $this->logger->notice("Transaction {$ticketMessageDTO->getTransactionId()} already has a ticket");
            return null;
        }

        return $this->ticketManager->createTicket($transaction);
    }

    /**
     * Получение нового тикета,
     * Присваивание ему модератора (пользователь с нужной ролью который инициировал этот вызов)
     * Перевод тикета в статус обработки
     *
     * @return Ticket|null
     */
    public function takeNewTicket(): ?Ticket {
        $ticket = $this->ticketManager->takeNewTicket();

        $authUser = $this->security->getUser();
        if (!$this->authorizationChecker->isGranted('take_ticket', $authUser)) {
            throw new UnprocessableEntityHttpException('This user can not take a ticket');
        }

        $user = $this->userManager->findByLogin($authUser->getUserIdentifier());
        if (!$user) {
            throw new UnprocessableEntityHttpException('User does not exists');
        }

        $ticket->setModerator($user);
        $ticket->setStatus(Status::PENDING);

        $this->ticketManager->updateTicket($ticket);

        return $ticket;
    }
}