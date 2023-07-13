<?php

namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

class ManageCountryDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 40, maxMessage: 'The name length should be less then 40')]
        public readonly string $name,
    )
    {
    }
}