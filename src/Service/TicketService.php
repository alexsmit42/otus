<?php

namespace App\Service;

use App\Consumer\Ticket\Input\TicketMessage;
use App\Entity\Ticket;
use App\Enum\Status;
use App\Manager\TicketManager;
use App\Manager\TransactionManager;
use App\Manager\UserManager;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class TicketService
{

    public function __construct(
        private readonly TransactionManager $transactionManager,
        private readonly TicketManager $ticketManager,
        private readonly UserManager $userManager,
        private readonly Security $security,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
    )
    {
    }

    public function createTicketFromAMQP(TicketMessage $ticketMessage): ?Ticket {
        $transaction = $this->transactionManager->getById($ticketMessage->getTransactionId());
        if ($transaction === null) {
            return null;
        }

        if ($this->ticketManager->findByTransaction($transaction)) {
            return null;
        }

        return $this->ticketManager->createTicket($transaction);
    }

    public function takeNewTicket(): ?Ticket {
        $ticket = $this->ticketManager->takeNewTicket();

        $authUser = $this->security->getUser();
        if (!$this->authorizationChecker->isGranted('take_ticket', $authUser)) {
            return null;
        }

        $user = $this->userManager->findByLogin($authUser->getUserIdentifier());
        if (!$user) {
            return null;
        }

        $ticket->setModerator($user);
        $ticket->setStatus(Status::PENDING);

        $this->ticketManager->updateTicket($ticket);

        return $ticket;
    }
}