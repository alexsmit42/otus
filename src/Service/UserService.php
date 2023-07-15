<?php

namespace App\Service;

use App\Entity\User;

class UserService
{

    public function __construct(
    )
    {
    }

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