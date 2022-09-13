<?php

namespace App\Security\Voter\Order;

use App\Interface\Cart\CartServiceInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;

class OrderVoter extends Voter
{
    public const FINALIZE = 'ORDER_FINALIZE';
    public const VIEW = 'ORDER_VIEW';
    public const CANCEL = 'ORDER_CANCEL';
    public const VIEW_ALL = 'ORDER_VIEW_ALL';

    private CartServiceInterface $cartService;
    private Security $security;

    public function __construct(Security $security, CartServiceInterface $cartService)
    {
        $this->cartService = $cartService;

        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::FINALIZE, self::VIEW, self::CANCEL
            , self::VIEW_ALL]);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if (!$user instanceof UserInterface || !$this->security->isGranted('ROLE_CUSTOMER')) {
            return false;
        }

        $accessIsGranted = match ($attribute){
            self::FINALIZE => $this->isAllowFinalize($subject['cartId'], $user),
            self::VIEW => $this->viewOrder($subject, $user),
            self::CANCEL => $this->isAllowCancel($subject, $user),
            self::VIEW_ALL => true,
        };

        return $accessIsGranted;
    }

    private function isAllowFinalize($subject, User $user)
    {
        $customer = $this->cartService->getCartById($subject)->getCustomer()->getId();
        return $user->getId() === $customer;
    }

    private function viewOrder($subject, User $user)
    {
        return $user->getId() === $subject->getCustomer()->getId();
    }

    private function isAllowCancel($subject, User $user)
    {
        return $user->getId() === $subject->getCustomer()->getId();
    }


}
