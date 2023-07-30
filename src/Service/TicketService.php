<?php

namespace App\Service;

use App\Consumer\Ticket\Input\TicketMessage;
use App\Entity\Ticket;
use App\Manager\TicketManager;
use App\Manager\TransactionManager;

class TicketService
{

    public function __construct(
        private readonly TransactionManager $transactionManager,
        private readonly TicketManager $ticketManager
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
}