<?php

namespace App\Security\Voter\Cart;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;
use App\Entity\Cart\Cart;

class CartVoter extends Voter
{
    public const HANDLE = 'CART_ACCESS';
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return $attribute == self::HANDLE
            && $subject instanceof Cart;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if (!$user instanceof UserInterface) {
            return false;
        }

        if (!$this->isAllow($subject, $user))
        {
            throw new \Exception(json_encode('access denied'));
        }

        return true;
    }

    private function isAllow($subject, $user)
    {
        return $user === $subject->getCustomer();
    }
}
