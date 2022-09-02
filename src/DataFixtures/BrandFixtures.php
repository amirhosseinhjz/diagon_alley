<?php

namespace App\DataFixtures;

use App\Entity\Brand\Brand;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BrandFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $brand1 = new Brand();
        $brand1->setName("brand1");

        $brand2 = new Brand();
        $brand2->setName("brand2");

        $manager->persist($brand1);
        $manager->flush();
    }
}