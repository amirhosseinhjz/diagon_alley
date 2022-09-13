<?php

namespace App\Service\OTP;

use App\Entity\User\User;
use App\Interface\Cache\CacheInterface;
use App\Message\SendSMSMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Doctrine\ORM\EntityManagerInterface;


class OTPService
{

    public function __construct(
        CacheInterface $cache,
        MessageBusInterface $messageBus,
        EntityManagerInterface $entityManager
    )
    {
        $this->cache = $cache;
        $this->messageBus = $messageBus;
        $this->entityManager = $entityManager;
        $this->expireAfter = (int)$_ENV['OTP_TTL'];
    }

    public function requestToken(User $user)
    {
        if ($user->isIsAuthenticated()) {
            throw new \Exception('User is already authenticated', 400);
        }
        $token = $this->generateToken();
        $this->setTokenOnUser($user, $token);
        $this->sendToken($user, $token);
    }

    private function setTokenOnUser(User $user, string $token)
    {
        $key = $this->getCacheKey($user->getId());
        $item = $this->cache->getAdapter()->getItem($key);
        $item->set($token);
        $item->expiresAfter($this->expireAfter);
        $this->cache->getAdapter()->save($item);
    }

    public function getUserToken(User $user): ?string
    {
        $key = $this->getCacheKey($user->getId());
        $item = $this->cache->getAdapter()->getItem($key);
        return $item->get();
    }

    public function verifyToken(User $user, $token)
    {
        $userToken = $this->getUserToken($user);
        if ($userToken != (string)$token) {
            throw new \Exception('Invalid token', 400);
        }
        $this->cache->getAdapter()->deleteItem($this->getCacheKey($user->getId()));
        $this->authorizeUser($user);
    }

    private function authorizeUser(User $user)
    {
        $user->setIsAuthenticated(true);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    private function sendToken(User $user, string $token)
    {
        $text = "Your OTP is: $token";
        $this->sendSMS($user->getPhoneNumber(), $text);
    }

    private function sendSMS(string $number, string $message)
    {
        $message = new SendSMSMessage($number, $message);
        $this->messageBus->dispatch($message);
    }

    private function generateToken()
    {
        return rand(100000, 999999);
    }

    private function getCacheKey(int $userId)
    {
        return 'OtpKey_' . $userId;
    }
}