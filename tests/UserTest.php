<?php

use App\Service\UserService\UserService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;



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
            'password' => '123456',
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

}