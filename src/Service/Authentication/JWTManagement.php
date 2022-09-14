<?php

namespace App\Service\Authentication;

use App\Event\AuthenticationEvent\TokenInvalidatedEvent;
use App\Interface\Authentication\JWTManagementInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class JWTManagement implements JWTManagementInterface
{
    protected $dispatcher;

    protected $JWTManager;

    protected $tokenStorageInterface;

    private $hasher;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        JWTTokenManagerInterface $JWTManager,
        TokenStorageInterface $tokenStorageInterface,
        UserPasswordHasherInterface $hasher,

    )
    {
        $this->JWTManager = $JWTManager;
        $this->dispatcher = $dispatcher;
        $this->tokenStorageInterface = $tokenStorageInterface;
        $this->hasher = $hasher;
    }

    public function getTokenUser(UserInterface $user)
    {
//        dd(1);
        $jwt = $this->JWTManager->create($user);
        $response = new Response();
        $event = new AuthenticationSuccessEvent(['token'=>$jwt],$user,$response);
        $this->dispatcher->dispatch($event,'lexik_jwt_authentication.on_authentication_success');
        return $event->getData();
    }

    public function invalidateToken()
    {
        $event = new TokenInvalidatedEvent($this->authenticatedUser());
        $this->dispatcher->dispatch($event,TokenInvalidatedEvent::NAME);
    }

    public function authenticatedUser()
    {
        if ($this->tokenStorageInterface->getToken())
        {
            return $this->tokenStorageInterface->getToken()->getUser();
        }
    }

    public function checkIfPasswordIsValid(UserInterface $user,Request $request)
    {
        $password = $request->toArray()['password'];
        if (!$this->hasher->isPasswordValid($user, $password)) {
            throw (new \Exception(json_encode('Invalid password')));
        }
    }
}