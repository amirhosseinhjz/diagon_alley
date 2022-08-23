<?php

namespace App\Listener\AuthenticationProcesses;

use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;

class JWTDecodedEventListener
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param JWTDecodedEvent $event
     *
     * @return void
     *
     */
    public function onJWTDecoded(JWTDecodedEvent $event)
    {
        $payload = $event->getPayload();
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            User::PAYLOAD_KEY_FOR_USERNAME => $payload[User::PAYLOAD_KEY_FOR_USERNAME]
        ]);
        if (
            $user &&
            $user->getTokenValidateAfter() instanceof \DateTime &&
            $payload['iat'] < $user->getTokenValidateAfter()->getTimestamp()
        ) {
            $event->markAsInvalid();
        }
    }
}
