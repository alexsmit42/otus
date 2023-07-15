<?php

namespace App\DTO\Response;

use App\Entity\Transaction;
use Symfony\Component\Validator\Constraints as Assert;

class TransactionResponseDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $id,

        #[Assert\NotBlank]
        #[Assert\Positive]
        public readonly float $amount,

        #[Assert\NotBlank]
        public readonly int $currency,

        #[Assert\NotBlank]
        #[Assert\Positive]
        public readonly float $user_amount,

        #[Assert\NotBlank]
        public readonly int $direction,

        #[Assert\NotBlank]
        public readonly int $status,

        #[Assert\NotBlank]
        public readonly string $payment_details,

        #[Assert\NotBlank]
        public readonly string $created_at,

        #[Assert\NotBlank]
        public readonly string $updated_at,

        #[Assert\NotBlank]
        public readonly int $payer,

        #[Assert\NotBlank]
        public readonly int $method,
    )
    {
    }

    public static function fromEntity(Transaction $transaction): self {
        return new self(...[
            'id' => $transaction->getId(),
            'amount' => $transaction->getAmount(),
            'currency' => $transaction->getCurrency()->getId(),
            'user_amount' => $transaction->getUserAmount(),
            'direction' => $transaction->getDirection()->value,
            'status' => $transaction->getStatus()->value,
            'payment_details' => $transaction->getPaymentDetails(),
            'created_at' => $transaction->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $transaction->getUpdatedAt()->format('Y-m-d H:i:s'),
            'payer' => $transaction->getPayer()->getId(),
            'method' => $transaction->getMethod()->getId(),
        ]);
    }
}