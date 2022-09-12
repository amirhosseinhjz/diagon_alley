<?php

namespace App\Security\Voter\Variant;

use App\Entity\User\User;
use App\Entity\Variant\Variant;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class VariantVoter extends Voter
{
    public const UPDATE = 'VARIANT_UPDATE';
    public const CREATE = 'VARIANT_CREATE';
    public const CONFIRM = 'VARIANT_CONFIRM';
    public const DENY = 'VARIANT_DENIED';
    public const SHOW = 'UNVERIFIED_VARIANT_SHOW';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::UPDATE, self::CREATE, self::CONFIRM, self::DENY]);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        $accessIsGranted = match ($attribute){
            self::CREATE => $this->create($user),
            self::CONFIRM => $this->confirm($user),
            self::DENY => $this->deny($user),
            self::UPDATE => $this->update($user,$subject),
            self::SHOW => $this->show($user)
        };

        return $accessIsGranted;
    }

    private function create(User $user){
        foreach ($user->getRoles() as $role){
            if($role == 'ROLE_SELLER')return true;
        }
        return false;
    }

    private function confirm(User $user){
        foreach ($user->getRoles() as $role){
            if($role == 'ROLE_ADMIN')return true;
        }
        return false;
    }

    private function deny(User $user){
        foreach ($user->getRoles() as $role){
            if($role == 'ROLE_ADMIN')return true;
        }
        return false;
    }

    private function update(User $user , Variant $variant){
        foreach ($user->getRoles() as $role){
            if( ($role == 'ROLE_SELLER' && $variant->getSeller()->getId() == $user->getId()) ||
                ($role == 'ROLE_ADMIN'))return true;
        }
        return false;
    }

    private function show(User $user){
        foreach ($user->getRoles() as $role){
            if($role == 'ROLE_ADMIN')return true;
        }
        return false;
    }
}
