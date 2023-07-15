<?php

namespace App\DTO\Request;

use App\Enum\Direction;
use App\Enum\Status;
use Symfony\Component\Validator\Constraints as Assert;

class GetTransactionsDTO
{
    public function __construct(
        #[Assert\Positive]
        public readonly ?int $method = null,

        public readonly ?Status $status = null,

        public readonly ?Direction $direction = null,
    )
    {
    }
}