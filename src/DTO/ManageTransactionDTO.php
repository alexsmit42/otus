<?php

namespace App\DTO;

use App\Entity\Currency;
use App\Entity\Method;
use App\Entity\Transaction;
use App\Entity\User;
use App\Enum\Direction;
use App\Enum\Status;
use App\Validator as CustomAssert;
use Symfony\Component\Validator\Constraints as Assert;

class ManageTransactionDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Positive]
        public float $amount = 0,

        #[Assert\NotBlank]
        public Currency $currency = new Currency(),

        #[Assert\NotBlank]
        public ?Direction $direction = null,

        #[Assert\NotBlank]
        public ?Status $status = Status::NEW,

        #[Assert\NotBlank]
        public User $payer = new User(),

        #[Assert\NotBlank]
        public Method $method = new Method(),

        #[Assert\NotBlank]
        #[CustomAssert\PaymentAccount]
        public string $paymentDetails = '',
    ) {
    }

    public static function fromEntity(Transaction $transaction): self
    {
        return new self(...[
            'amount'         => $transaction->getAmount(),
            'currency'       => $transaction->getCurrency(),
            'direction'      => $transaction->getDirection(),
            'status'         => $transaction->getStatus(),
            'payer'          => $transaction->getPayer(),
            'method'         => $transaction->getMethod(),
            'paymentDetails' => $transaction->getPaymentDetails(),
        ]);
    }
}