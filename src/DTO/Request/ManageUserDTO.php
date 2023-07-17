<?php

namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

class ManageUserDTO
{
    public function __construct(
        #[Assert\Length(max: 50, maxMessage: 'The login length should be less than 50')]
        public readonly ?string $login = null,

        #[Assert\Length(max: 20, maxMessage: 'The password length should be less than 20')]
        public readonly ?string $password = null,

        #[Assert\Positive]
        public readonly ?int $currency = null,

        #[Assert\Positive]
        public readonly ?int $country = null,

        #[Assert\Type('array')]
        public array $roles = []
    ) {
    }
}