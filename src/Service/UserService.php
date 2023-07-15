<?php

namespace App\Service;

use App\DTO\Request\GetTransactionsDTO;
use App\Entity\User;
use App\Manager\MethodManager;
use App\Manager\UserManager;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class UserService
{

    public function __construct(
        private readonly UserManager $userManager,
        private readonly MethodManager $methodManager,
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

    public function findTransactions(User $user, GetTransactionsDTO $dto): array {
        $method = null;
        if ($dto->method) {
            $method = $this->methodManager->getById($dto->method) ?? throw new UnprocessableEntityHttpException('Method does not exists');
        }

        return $this->userManager->findTransactions($user, $method, $dto->direction, $dto->status);
    }
}