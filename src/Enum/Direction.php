<?php

namespace App\Enum;

/**
 * Type of Transaction
 */
enum Direction: int
{
    case DEPOSIT = 1; // to balance
    case WITHDRAW = 2; // from balance

    public static function fromString(string $string): ?self {
        return match ($string) {
            'deposit' => Direction::DEPOSIT,
            'withdraw' => Direction::WITHDRAW,
            default => null,
        };
    }
}