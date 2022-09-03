<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CartVoter extends Voter
{
    public const EDIT = '_EDIT';
    public const VIEW = '_VIEW';
    public const BACK = '_BACK';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof \App\Entity\Cart\Cart;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Cart $cart */
        $cart = $subject;

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        switch ($attribute) {
            case self::EDIT:
                return ($user->getUserIdentifier() === $cart->getUserId() && 
                ($cart->getStatus() === "INIT"));
            case self::VIEW:
                return ($user->getUserIdentifier() === $cart->getUserId()); 
            case self::BACK:
                return (($user->getUserIdentifier() === $cart->getUserId()) &&
                ($cart->getStatus() === "PENDING" || $cart->getStatus() === "EXPIRED"));
        }

        return false;
    }
}
