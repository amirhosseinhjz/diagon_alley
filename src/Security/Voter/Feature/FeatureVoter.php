<?php

namespace App\Security\Voter\Feature;

use App\Entity\User\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class FeatureVoter extends Voter
{
    public const CREATE = 'FEATURE_CREATE';
    public const SHOW = 'FEATURE_SHOW';
    private $security;

    public function __construct(Security $security){
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::CREATE , self::SHOW]);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        if($this->security->isGranted('ROLE_ADMIN')){
            return true;
        }

        $accessIsGranted = match ($attribute){
            self::CREATE => $this->create($user),
            self::SHOW => $this->show($user)
        };

        return $accessIsGranted;
    }

    private function create(User $user){
        foreach ($user->getRoles() as $role){
            if($role == 'ROLE_ADMIN')return true;
        }
        return false;
    }

    private function show(User $user){
        foreach ($user->getRoles() as $role){
            if($role == 'ROLE_ADMIN' || $role == 'ROLE_SELLER')return true;
        }
        return false;
    }
}
