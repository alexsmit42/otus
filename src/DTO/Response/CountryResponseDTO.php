<?php

namespace App\DTO\Response;

use Symfony\Component\Validator\Constraints as Assert;

class CountryResponseDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(40)]
        public readonly string $name,
    )
    {
    }
}