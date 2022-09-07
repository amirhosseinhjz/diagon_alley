<?php

namespace App\Security\Voter\Cart;

use App\Entity\Cart\CartItem;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CartItemVoter extends Voter
{
    public const EDIT = '_EDIT';
    public const VIEW = '_VIEW';

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof \App\Entity\Cart\CartItem;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var CartItem $item*/
        $item = $subject;

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        switch ($attribute) {
            case self::EDIT:
                return $user->getUserIdentifier() === $item->getCart()->getCustomer()->getId();
            case self::VIEW:
                if($this->security->isGranted('ROLE_SELLER')) {
                    return true;
                }
                return $user->getUserIdentifier() === $item->getCart()->getCustomer()->getId();
        }

        return false;
    }
}
