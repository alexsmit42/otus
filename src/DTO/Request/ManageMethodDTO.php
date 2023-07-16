<?php

namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

class ManageMethodDTO
{
    public function __construct(
        #[Assert\Length(max: 40, maxMessage: 'The name length should be less than 40')]
        public readonly ?string $name = null,

        #[Assert\Positive]
        public readonly ?float $min_limit = null,

        #[Assert\Positive]
        public readonly ?float $max_limit = null,
    )
    {
    }
}