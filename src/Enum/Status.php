<?php

namespace App\Enum;

enum Status: int
{
    case NEW = 1;
    case PENDING = 2;
    case FAIL = 3;
    case SUCCESS = 4;
}