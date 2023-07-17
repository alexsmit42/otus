<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserTransactionVoter extends Voter
{
    private const GET_TRANSACTION = 'get_transaction';

    public function __construct(
        private Security $security,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute !== self::GET_TRANSACTION) {
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
        if ($this->security->isGranted('ROLE_SUPPORT')) {
            return true;
        }

        if ($subject === null || !$subject instanceof User) {
            return false;
        }

        $user = $token->getUser();

        return $user->getUserIdentifier() === $subject->getLogin();
    }
}