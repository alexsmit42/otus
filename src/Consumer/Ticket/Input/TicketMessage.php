<?php

namespace App\Consumer\Ticket\Input;

use Symfony\Component\Validator\Constraints as Assert;

class TicketMessage
{
    #[Assert\Type('numeric')]
    #[Assert\Positive]
    private int $transactionId;

    public static function createFromQueue(string $messageBody): self
    {
        $message               = json_decode($messageBody, true, 512, JSON_THROW_ON_ERROR);
        $result                = new self();
        $result->transactionId = $message['transactionId'];

        return $result;
    }

    public function getTransactionId(): int
    {
        return $this->transactionId;
    }
}