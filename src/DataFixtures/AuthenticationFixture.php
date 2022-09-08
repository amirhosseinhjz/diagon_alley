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
        $seller->setName('gigi');
        $seller->setEmail('ee44@gmail.com');
        $seller->setPassword($this->passHasher->hashPassword($seller,'123456789Aa@'));
        $seller->setPhoneNumber('09128464485');
        $seller->setLastname('eeeccc');
        $seller->setShopSlug('hvfdoe');
        $seller->setRoles(['ROLE_SELLER']);
        $manager->persist($seller);

        $customer = new Customer();
        $customer->setName('lili');
        $customer->setLastname('hdhhd');
        $customer->setEmail('eee@gmail.com');
        $customer->setPassword($this->passHasher->hashPassword($customer,'123456789Aa@'));
        $customer->setPhoneNumber('09128468486');
        $customer->setRoles(['ROLE_CUSTOMER']);
        $manager->persist($customer);

        $seller1 = new Seller();
        $seller1->setName('kiki');
        $seller1->setEmail('kiki@gmail.com');
        $seller1->setPassword($this->passHasher->hashPassword($seller1,'123456789Aa@'));
        $seller1->setPhoneNumber('09128464487');
        $seller1->setLastname('eeeccc');
        $seller1->setShopSlug('wwwwwggkkkkk');
        $seller1->setRoles(['ROLE_SELLER']);
        $manager->persist($seller1);

        $seller2 = new Seller();
        $seller2->setName('sisi');
        $seller2->setEmail('sisi@gmail.com');
        $seller2->setPassword($this->passHasher->hashPassword($seller2,'123456789Aa@'));
        $seller2->setPhoneNumber('09128464488');
        $seller2->setLastname('eeeccc');
        $seller2->setShopSlug('wwdsgggg');
        $seller2->setRoles(['ROLE_SELLER']);
        $manager->persist($seller2);
        $manager->flush();

        $this->addReference('seller', $seller);
        $this->addReference('seller1', $seller1);
        $this->addReference('seller2', $seller2);

    }

    public static function getGroups(): array
    {
        return ['authFixGroup'];
    }
}
