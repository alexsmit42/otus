<?php

namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

class ManageCurrencyDTO
{
    public function __construct(
        #[Assert\Currency]
        public readonly ?string $iso = null,

        #[Assert\NotBlank]
        #[Assert\Positive]
        public readonly ?float $rate = 0,
    )
    {
    }
}