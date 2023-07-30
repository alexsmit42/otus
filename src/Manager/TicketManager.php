<?php

namespace App\Manager;

use App\Entity\Ticket;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;

class TicketManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function createTicket(Transaction $transaction): ?Ticket {
        $ticket = new Ticket();
        $ticket->setTransaction($transaction);
        $this->entityManager->persist($ticket);
        $this->entityManager->flush();

        return $ticket;
    }

    public function findByTransaction(Transaction $transaction): ?Ticket {
        return $this->entityManager->getRepository(Ticket::class)->findOneBy(['transaction' => $transaction]);
    }
}