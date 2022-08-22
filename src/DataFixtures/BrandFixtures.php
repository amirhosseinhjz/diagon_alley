<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BrandFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $brand = new Brand();
        $brand->setName("apple");

        $manager->persist($brand);
        $manager->flush();
    }
}