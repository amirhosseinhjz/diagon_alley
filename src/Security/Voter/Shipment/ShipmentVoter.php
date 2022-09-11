<?php

namespace App\Security\Voter;

use App\Entity\Shipment\Shipment;
use App\Entity\User\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;


class ShipmentVoter extends Voter
{
    public const HANDLE = 'SHIPMENT_ACCESS';
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::HANDLE])
            && $subject instanceof \App\Entity\Shipment\Shipment;
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
        return $user === $subject->getSeller();
    }
}
