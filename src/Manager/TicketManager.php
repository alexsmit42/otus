<?php

namespace App\Manager;

use App\Entity\Ticket;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class TicketManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function createTicket(Transaction $transaction, User $moderator): ?Ticket {
        $ticket = new Ticket();
        $ticket->setTransaction($transaction);
        $ticket->setModerator($moderator);
        $this->entityManager->persist($ticket);
        $this->entityManager->flush();
    }

    public function findByTransactionId(int $transactionId): ?Ticket {
        return $this->entityManager->getRepository(Ticket::class)->findOneBy(['transaction_id' => $transactionId]);
    }
}