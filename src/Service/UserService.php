<?php

namespace App\Service;

use App\Entity\User;
use App\Enum\Direction;
use App\Manager\MethodManager;
use App\Manager\UserManager;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class UserService
{
    public const  CACHE_TAG_METHODS_USER_PREFIX = 'methods_user_';
    public const  CACHE_TAG_METHODS_USERS       = 'methods_users';
    private const EXPIRE_CACHE                  = 60 * 60;

    public function __construct(
        private readonly UserManager $userManager,
        private readonly TagAwareCacheInterface $cache,
    ) {
    }

    /**
     * get all user's payment_details from successful transactions (confident)
     * @param User $user
     * @return array
     */
    public function getCheckedPaymentDetails(User $user): array
    {
        return [];
    }

    public function getAvailableMethods(User $user, Direction $direction = Direction::DEPOSIT): array
    {
        $cacheKey = "methods.user.{$user->getId()}.{$direction->value}";

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($user, $direction) {
            $item->tag([
                self::CACHE_TAG_METHODS_USER_PREFIX . $user->getId(), // if change user's country
                self::CACHE_TAG_METHODS_USERS, // if add/remove country from method
                MethodManager::CACHE_TAG, // if add/remove method
            ]);
            $item->expiresAfter(self::EXPIRE_CACHE);

            return $this->userManager->findAvailableMethods($user, $direction);
        });
    }
}