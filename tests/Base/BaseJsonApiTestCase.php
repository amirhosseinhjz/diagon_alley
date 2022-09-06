<?php
namespace App\Tests\Base;

use ApiTestCase\JsonApiTestCase;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BaseJsonApiTestCase extends JsonApiTestCase
{
    protected array $defaultUser = ['username'=>'09128464485' ,'password'=>'123456789'];

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

    /**
     * @return false|string
     */
    public function loginDefaultUserGetToken(): string|false
    {
        $this->client->request(
            'POST',
            'http://localhost:70/api/user/login',
            [],
            [],
            [],
            json_encode($this->defaultUser)
        );

        $response = $this->client->getResponse()->getContent();

        return $response;
    }
    
    public function getValidator(): \PHPUnit\Framework\MockObject\MockObject|ValidatorInterface
    {
        return $this->getMockBuilder(
            ValidatorInterface::class
        )->getMockForAbstractClass();
    }
}