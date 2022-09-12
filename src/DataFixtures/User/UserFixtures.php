<?php

namespace App\DataFixtures\User;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class UserFixtures extends Fixture
{
    public function loadSeller(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $seller = new User\Seller();
            $seller->setShopSlug('shop'.$i);
            $seller->setPhoneNumber('+9891'.$i.'1234567');
            $seller->setEmail("seller$i@seller.com");
            $seller->setPassword('seller');
            $seller->setRoles(['ROLE_SELLER']);
            $seller->setName('seller'.$i);
            $seller->setLastName('seller'.$i.'lastname');
            $manager->persist($seller);
    }
        $manager->flush();
    }

    public function loadCustomer(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $customer = new User\Customer();
            $customer->setPhoneNumber('+9891' . $i . '7594567');
            $customer->setEmail("customer$i@customer.com");
            $customer->setPassword('customer');
            $customer->setRoles(['ROLE_CUSTOMER']);
            $customer->setName('customer' . $i);
            $customer->setLastName('customer' . $i . 'lastname');
            $manager->persist($customer);
        }
        $manager->flush();

    }

    public function loadAdmin(ObjectManager $manager)
    {
        for ($i = 0; $i < 3; $i++) {
            $admin = new User\Admin();
            $admin->setPhoneNumber('+9891' . $i . '7594548');
            $admin->setEmail("admin$i@admin.com");
            $admin->setPassword('admin');
            $admin->setRoles(['ROLE_ADMIN']);
            $admin->setName('admin' . $i);
            $admin->setLastName('admin' . $i . 'lastname');
            $manager->persist($admin);
        }
        $manager->flush();
    }

    public function load(ObjectManager $manager)
    {
        $this->loadSeller($manager);
        $this->loadCustomer($manager);
        $this->loadAdmin($manager);
    }
}