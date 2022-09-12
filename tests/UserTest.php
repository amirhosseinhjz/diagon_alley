<?php

use App\Service\UserService\UserService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


/**
 * @group User
 */
class UserTest extends KernelTestCase
{
    private UserService $userService;

    protected function setUp() : void
    {
        $kernel = self::bootKernel();
        $this->userService = $kernel->getContainer()->get(UserService::class);
    }

    public function testCreateUser()
    {
        $userData = ['roles' => ['ROLE_SELLER'],
            'phoneNumber' => '+989333046603',
            'password' => '123456DFFd@',
            'shopSlug' => 'test',
            'email' => 'seller@user.com',
            'name' => 'seller_name',
            'lastName' => 'sellerLastName'
        ];
        $user = $this->userService->createFromArray($userData);
        $user = $this->userService->getUserById($user->getId());
        $this->assertEquals($userData['roles'], $user->getRoles());
        $this->assertEquals($userData['phoneNumber'], $user->getPhoneNumber());
        $this->assertEquals($userData['password'], $user->getPassword());
        $this->assertEquals($userData['shopSlug'], $user->getShopSlug());
        $this->assertEquals($userData['email'], $user->getEmail());
        $this->assertEquals($userData['name'], $user->getName());
        $this->assertEquals($userData['lastName'], $user->getLastName());
    }

    public function testUpdateUser()
    {
        $data = ['name' => 'new_name',
            'lastName' => 'new_lastName',
//            'phoneNumber' => '+989333046603',
//            'password' => '123456',
//            'shopSlug' => 'test'
            ];
        $this->userService->updateUserById(1, $data);
        $user = $this->userService->getUserById(1);
        $this->assertEquals($data['name'], $user->getName());
        $this->assertEquals($data['lastName'], $user->getLastName());
//        $this->assertEquals($data['phoneNumber'], $user->getPhoneNumber());
//        $this->assertEquals($data['password'], $user->getPassword());
    }

    public function testUpdateEmail()
    {
        $email = 'newmail@newmail.com';
        $this->userService->updateEmailById(1, $email);
        $user = $this->userService->getUserById(1);
        $this->assertEquals($email, $user->getEmail());
        $wrongEmail = 'lksdihivo';
        $this->expectException(\Exception::class);
        $this->userService->updateEmailById(1, $wrongEmail);
        $this->assertEquals($email, $user->getEmail());
    }

    public function testUpdatePhoneNumber()
    {
        $phoneNumber = '+989333046703';
        $this->userService->updatePhoneNumberById(1, $phoneNumber);
        $user = $this->userService->getUserById(1);
        $this->assertEquals($phoneNumber, $user->getPhoneNumber());
        $wrongPhoneNumber = 'lksdihivo';
        $this->expectException(\Exception::class);
        $this->userService->updatePhoneNumberById(1, $wrongPhoneNumber);
        $this->assertEquals($phoneNumber, $user->getPhoneNumber());
    }

}