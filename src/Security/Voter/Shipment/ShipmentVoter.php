<?php

namespace App\Security\Voter\Shipment;

use App\Entity\User\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;


class ShipmentVoter extends Voter
{
    public const SHIPMENT = 'SHIPMENT_ACCESS';
    public const SHIPMENT_ITEM = 'SHIPMENT_ITEM_ACCESS';
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::SHIPMENT,self::SHIPMENT_ITEM]);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if (!$user instanceof UserInterface || !$this->security->isGranted('ROLE_SELLER')) {
            return false;
        }

        $accessIsGranted = match ($attribute){
            self::SHIPMENT => $this->isAllow($subject, $user),
            self::SHIPMENT_ITEM => $this->updateAccess($subject , $user)
        };

        return $accessIsGranted;
    }

    private function isAllow($subject, User $user)
    {
        return $user->getId() === $subject->getSeller()->getId();
    }

    private function updateAccess($subject, User $user)
    {
        return $user->getId() === $subject->getShipment()->getSeller()->getId();
    }
}
