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
        $province1 = new Address\AddressProvince();
        $province1->setName("Tehran");
        $manager->persist($province1);
        
        $province2 = new Address\AddressProvince();
        $province2->setName("Kerman");
        $manager->persist($province2);

        $province3 = new Address\AddressProvince();
        $province3->setName("Mazandaran");
        $manager->persist($province3);

        $manager->flush();
    }
    
    public function loadCity(ObjectManager $manager): void
    {
        $city1 = new Address\AddressCity();
        $city1->setName("Damavand");
        $province1 = $manager->getRepository(Address\AddressProvince::class)->findOneByName("Tehran");
        $city1->setProvince($province1);
        $manager->persist($city1);

        $city2 = new Address\AddressCity();
        $city2->setName("Kerman");
        $province2 = $manager->getRepository(Address\AddressProvince::class)->findOneByName("Kerman");
        $city2->setProvince($province2);
        $manager->persist($city2);

        $city3 = new Address\AddressCity();
        $city3->setName("Babol");
        $province3 = $manager->getRepository(Address\AddressProvince::class)->findOneByName("Mazandaran");
        $city3->setProvince($province3);
        $manager->persist($city3);
        
        $manager->flush();
    }
}
