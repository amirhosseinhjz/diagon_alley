<?php

namespace App\Tests\AuthenticationTest;

use App\Entity\User\User;
use App\Service\Authentication\JWTManagement;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @group jwt
 */
class UserAuthenticationProcessTest extends TestCase
{

    /**
     * @param bool $hasToken
     * @return void
     */
    public function testAuthenticatedUser(bool $hasToken = true)
    {
        $user = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->getMock();

        $hasher = $this->getUserPasswordHasher();

        $dispatcher = $this->getDispatchObject();

        $tokenManagement = $this->getJWTTokenManager();

        $tokenStorageInterface = $this->getTokenStorageMock();

        $tokenInterface = $this->getTokenInterface();

        $tokenStorageInterface->expects($this->exactly(2))
            ->method('getToken')
            ->willReturn($hasToken ? $tokenInterface : null);

        $tokenInterface->expects($hasToken ?
            $this->once() : $this->never()
        )
            ->method('getUser')
            ->willReturn($user);

        (new JWTManagement(
            $dispatcher,
            $tokenManagement,
            $tokenStorageInterface,
            $hasher)
        )->authenticatedUser();
    }

    public function testGetUserToken()
    {
        $dispatcher = $this->getDispatchObject();

        $hasher = $this->getUserPasswordHasher();

        $tokenManagement = $this->getJWTTokenManager();

        $tokenStorageInterface = $this->getTokenStorageMock();

        $tokenInterface = $this->getTokenInterface();

        $requestMock = $this->getMockBuilder(Request::class)
            ->getMock();

        $userInterface = $this->getMockBuilder(UserInterface::class)
            ->getMockForAbstractClass();

        $tokenManagement->expects($this->once())
            ->method('create')
            ->willReturn($tokenInterface);

        $dispatcher->expects($this->once())
            ->method('dispatch');

        (new JWTManagement(
            $dispatcher,
            $tokenManagement,
            $tokenStorageInterface,
            $hasher)
        )->getTokenUser($userInterface,$requestMock);
    }

    public function getTokenStorageMock()
    {
        return $this->getMockBuilder(
            TokenStorageInterface::class
        )->getMockForAbstractClass();
    }

     /**
     * @return \PHPUnit\Framework\MockObject\MockObject|EventDispatcherInterface
     */
     public function getDispatchObject(): EventDispatcherInterface|\PHPUnit\Framework\MockObject\MockObject
    {
        $dispatcher = $this->getMockBuilder(
            EventDispatcherInterface::class
        )->getMockForAbstractClass();
        return $dispatcher;
    }

    /**
     * @return JWTTokenManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    public function getJWTTokenManager(): \PHPUnit\Framework\MockObject\MockObject|JWTTokenManagerInterface
    {
        $tokenManagement = $this->getMockBuilder(
            JWTTokenManagerInterface::class
        )->getMockForAbstractClass();
        return $tokenManagement;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|TokenInterface
     */
    public function getTokenInterface(): TokenInterface|\PHPUnit\Framework\MockObject\MockObject
    {
        $tokenInterface = $this->getMockBuilder(
            TokenInterface::class
        )->getMockForAbstractClass();
        return $tokenInterface;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|UserPasswordHasherInterface
     */
    public function getUserPasswordHasher(): \PHPUnit\Framework\MockObject\MockObject|UserPasswordHasherInterface
    {
        $hasher = $this->getMockBuilder(UserPasswordHasherInterface::class)
            ->getMockForAbstractClass();
        return $hasher;
    }
}