<?php

namespace App\DataFixtures\Address;

use App\Entity\Address;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AddressFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->loadProvince($manager);
        $this->loadCity($manager);
    }

    public function loadProvince(ObjectManager $manager): void
    {
        $province = new Address\AddressProvince();
        $province->setName("Tehran");

        $manager->persist($province);
        $manager->flush();
    }
    
    public function loadCity(ObjectManager $manager): void
    {
        $city = new Address\AddressCity();
        $city->setName("Damavand");
        $province = $manager->getRepository(Address\AddressProvince::class)->findOneByName("Tehran");
        $city->setProvince($province);

        $manager->persist($city);
        $manager->flush();
    }
}
