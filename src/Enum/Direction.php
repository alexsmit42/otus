<?php

namespace App\Enum;

/**
 * Type of Transaction
 */
enum Direction: int
{
    case DEPOSIT = 1; // to balance
    case WITHDRAW = 2; // from balance
}