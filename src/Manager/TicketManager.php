<?php

namespace App\Manager;

use App\Entity\Ticket;
use App\Entity\Transaction;
use App\Enum\Status;
use Doctrine\ORM\EntityManagerInterface;

class TicketManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    private function save(Ticket $ticket): bool
    {
        $this->entityManager->persist($ticket);
        $this->entityManager->flush();

        return true;
    }

    public function createTicket(Transaction $transaction): ?Ticket {
        $ticket = new Ticket();
        $ticket->setTransaction($transaction);
        $this->save($ticket);

        return $ticket;
    }

    public function updateTicket(Ticket $ticket): ?Ticket {
        $this->save($ticket);

        return $ticket;
    }

    public function findByTransaction(Transaction $transaction): ?Ticket {
        return $this->entityManager->getRepository(Ticket::class)->findOneBy(['transaction' => $transaction]);
    }

    public function takeNewTicket(): ?Ticket {
        return $this->entityManager->getRepository(Ticket::class)->findOneBy(['status' => Status::NEW], ['created_at' => 'ASC']);
    }
}