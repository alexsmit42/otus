<?php

namespace App\DTO\Request;

use App\DTO\Response\TransactionResponseDTO;
use App\Entity\Ticket;
use Symfony\Component\Validator\Constraints as Assert;

class TicketResponseDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $id,

        #[Assert\NotBlank]
        public readonly int $status,

        #[Assert\NotBlank]
        public readonly string $created_at,

        #[Assert\NotBlank]
        public readonly string $updated_at,

        #[Assert\NotBlank]
        public readonly int $moderator,

        public readonly TransactionResponseDTO $transaction,
    ) {
    }

    public static function fromEntity(Ticket $ticket): self
    {
        return new self(...[
            'id'          => $ticket->getId(),
            'status'      => $ticket->getStatus()->value,
            'created_at'  => $ticket->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at'  => $ticket->getUpdatedAt()->format('Y-m-d H:i:s'),
            'moderator'   => $ticket->getModerator()->getId(),
            'transaction' => TransactionResponseDTO::fromEntity($ticket->getTransaction()),
        ]);
    }
}