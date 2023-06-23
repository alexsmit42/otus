<?php

namespace App\Enum;

/**
 * Status for Transaction and Purchase
 */
enum Status: int
{
    case NEW = 1;
    case PENDING = 2;
    case FAIL = 3;
    case SUCCESS = 4;
}