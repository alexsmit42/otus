<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{

    private const VIEW_AVAILABLE_METHODS = 'view_available_methods';
    private const UPDATE_PASSWORD        = 'update_password';
    private const GET_USER               = 'get_user';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::VIEW_AVAILABLE_METHODS, self::UPDATE_PASSWORD, self::GET_USER])) {
            return false;
        }

        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    /**
     * @param string         $attribute
     * @param User           $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        return $subject->getLogin() === $user->getUserIdentifier();
    }
}