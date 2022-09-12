<?php

namespace App\Event\AuthenticationEvent;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class TokenInvalidatedEvent extends Event
{
    public const NAME = 'token.invalidated.event';

    private UserInterface $user;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}