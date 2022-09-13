<?php

namespace App\Security\Voter\Category;

use App\Entity\User\User;
use App\Entity\Category\Category;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CategoryVoter extends Voter
{
    public const CRUD = 'CATEGORY_CRUD';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::CRUD]);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        $accessIsGranted = match ($attribute) {
            self::CRUD => $this->crud($user)
        };

        return $accessIsGranted;
    }

    private function crud(User $user)
    {
        foreach ($user->getRoles() as $role) {
            if ($role == 'ROLE_ADMIN') return true;
        }
        return false;
    }
}