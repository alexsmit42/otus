<?php

namespace App\Consumer\Ticket;

use App\Consumer\Ticket\Input\TicketMessage;
use App\Service\TicketService;
use JsonException;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Consumer implements ConsumerInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly TicketService $ticketService,
    ) {
    }

    public function execute(AMQPMessage $msg): int
    {
        try {
            $ticketMessage = TicketMessage::createFromQueue($msg->getBody());
            $errors        = $this->validator->validate($ticketMessage);
            if ($errors->count() > 0) {
                return $this->reject((string)$errors);
            }
        } catch (JsonException $e) {
            return $this->reject($e->getMessage());
        }

        if ($this->ticketService->createTicketFromAMQP($ticketMessage) === null) {
            $this->reject('Ticket was not created');
        }

        return self::MSG_ACK;
    }

    private function reject(string $error): int
    {
        echo "Incorrect message: $error";

        return self::MSG_REJECT;
    }
}