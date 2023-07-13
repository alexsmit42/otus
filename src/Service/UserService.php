<?php

namespace App\Service;

use App\Entity\User;
use App\Enum\Direction;

class UserService
{

    /**
     * get all user's from successful transactions (confident)
     *
     * @param User $user
     * @return array
     */
    public function getCheckedPaymentDetails(User $user): array {
        return [];
    }
}