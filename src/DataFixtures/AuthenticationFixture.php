<?php

namespace App\DataFixtures;

use App\Entity\User\Customer;
use App\Entity\User\Seller;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthenticationFixture extends Fixture implements FixtureGroupInterface
{
    protected $passHasher;
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->passHasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $seller = new Seller();
        $seller->setName('4rter');
        $seller->setEmail('ee44@gmail.com');
        $seller->setPassword($this->passHasher->hashPassword($seller,'123456789*zZ'));
        $seller->setPhoneNumber('09128464485');
        $seller->setLastname('eeeccc');
        $seller->setShopSlug('hvfdoe');
        $seller->setRoles(['ROLE_SELLER']);
        $manager->persist($seller);

        $customer = new Customer();
        $customer->setName('lili');
        $customer->setLastname('hdhhd');
        $customer->setEmail('eee@gmail.com');
        $customer->setPassword($this->passHasher->hashPassword($customer,'123456789*zZ'));
        $customer->setPhoneNumber('09128468486');
        $customer->setRoles(['ROLE_CUSTOMER']);
        $manager->persist($customer);
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['authFixGroup'];
    }
}
