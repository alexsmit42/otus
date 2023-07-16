<?php

namespace App\DTO\Request;

use App\Enum\Direction;
use App\Validator as CustomAssert;
use Symfony\Component\Validator\Constraints as Assert;

class ManageTransactionDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Positive]
        public float $amount = 0,

        #[Assert\NotBlank]
        public ?int $currency = null,

        #[Assert\NotBlank]
        public ?Direction $direction = null,

        #[Assert\NotBlank]
        public ?int $payer = null,

        #[Assert\NotBlank]
        public ?int $method = null,

        #[Assert\NotBlank]
        #[CustomAssert\PaymentAccount]
        public string $payment_details = '',
    )
    {
    }
}