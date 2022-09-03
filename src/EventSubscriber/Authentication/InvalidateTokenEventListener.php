<?php

namespace App\EventSubscriber\Authentication;


use App\Event\AuthenticationEvent\TokenInvalidatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InvalidateTokenEventListener implements EventSubscriberInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TokenInvalidatedEvent::NAME => 'invalidate'
        ];
    }

    public function invalidate(TokenInvalidatedEvent $event)
    {
        $user = $event->getUser();
        $user->setTokenValidateAfter(new \DateTime());
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}