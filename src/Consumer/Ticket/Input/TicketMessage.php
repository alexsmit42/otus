<?php

namespace App\Consumer\Ticket\Input;

use Symfony\Component\Validator\Constraints as Assert;

class TicketMessage
{
    public function __construct(
        #[Assert\Type('numeric')]
        #[Assert\Positive]
        private readonly int $transactionId
    ) {
    }

    public static function createFromQueue(string $messageBody): self
    {
        $message = json_decode($messageBody, true, 512, JSON_THROW_ON_ERROR);

        $transactionId = $message['transactionId'];

        return new self($transactionId);
    }

    public function toAMQPMessage(): string
    {
        return json_encode(['transactionId' => $this->transactionId], JSON_THROW_ON_ERROR);
    }

    public function getTransactionId(): int
    {
        return $this->transactionId;
    }
}