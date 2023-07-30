<?php

namespace App\Service;

use App\Consumer\Ticket\Input\TicketMessage;
use App\Entity\Ticket;
use App\Entity\User;
use App\Manager\TicketManager;
use App\Manager\TransactionManager;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class TicketService
{

    public function __construct(
        private readonly TransactionManager $transactionManager,
        private readonly TicketManager $ticketManager,
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

        /** @var User $user */
        $user = $this->security->getUser();
        if (!$this->authorizationChecker->isGranted('create_ticket', $user)) {
            return null;
        }

        return $this->ticketManager->createTicket($transaction, $user);
    }
}