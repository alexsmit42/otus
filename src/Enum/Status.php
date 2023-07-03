<?php

namespace App\Enum;

/**
 * Status for Transaction
 */
enum Status: int
{
    case NEW = 1;
    case PENDING = 2;
    case FAIL = 3;
    case SUCCESS = 4;

    public function isFinal(): bool
    {
        return match($this) {
            Status::NEW, Status::PENDING => false,
            Status::FAIL, Status::SUCCESS => true,
        };
    }
}