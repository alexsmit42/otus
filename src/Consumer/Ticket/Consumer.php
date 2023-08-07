<?php

namespace App\Consumer\Ticket;

use App\DTO\Message\TicketMessageDTO;
use App\Service\TicketService;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Consumer implements ConsumerInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly TicketService $ticketService,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function execute(AMQPMessage $msg): int
    {
        try {
            $ticketMessageDTO = TicketMessageDTO::createFromQueue($msg->getBody());
            $errors        = $this->validator->validate($ticketMessageDTO);
            if ($errors->count() > 0) {
                return $this->reject((string)$errors);
            }
        } catch (JsonException $e) {
            return $this->reject($e->getMessage());
        }

        if ($this->ticketService->createTicketFromDTO($ticketMessageDTO) === null) {
            $this->reject('Ticket was not created');
        }

        $this->entityManager->clear();
        $this->entityManager->getConnection()->close();

        return self::MSG_ACK;
    }

    private function reject(string $error): int
    {
        echo "Incorrect message: $error";

        return self::MSG_REJECT;
    }
}