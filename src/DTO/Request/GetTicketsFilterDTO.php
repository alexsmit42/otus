<?php

namespace App\DTO\Request;

use App\Enum\Direction;
use App\Enum\Status;
use Symfony\Component\Validator\Constraints as Assert;

class GetTicketsFilterDTO
{
    public function __construct(
        #[Assert\Positive]
        public readonly ?int $payer = null,

        #[Assert\Positive]
        public readonly ?int $method = null,

        public readonly ?Status $status = null,

        public readonly ?Direction $direction = null,
    )
    {
    }
}