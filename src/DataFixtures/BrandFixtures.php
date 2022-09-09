<?php

namespace App\DataFixtures;

use App\Entity\Brand\Brand;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class BrandFixtures extends Fixture implements FixtureGroupInterface
{
    public const UPDATE_NAME = 'ToBeUpdated';
    public const DELETE_NAME = 'ToBeDeleted';
    public const READ_NAME = 'brandName';

    public static function getGroups(): array
    {
        return ['brand'];
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadBrandsForRead($manager);
        $this->loadBrandForUpdate($manager);
        $this->loadBrandForDelete($manager);
    }

    public function loadBrandsForRead(ObjectManager $manager)
    {
        $brand = new Brand();
        $brand->setName(self::READ_NAME);
        $manager->persist($brand);

        $brand1 = new Brand();
        $brand1->setName("brand1");
        $manager->persist($brand1);

        $brand2 = new Brand();
        $brand2->setName("brand2");
        $manager->persist($brand2);

        $manager->flush();
    }

    public function loadBrandForUpdate(ObjectManager $manager)
    {
        $brand = new Brand();
        $brand->setName(self::UPDATE_NAME);
        $brand->setDescription('initial description');
        $manager->persist($brand);
        $manager->flush();
    }

    public function loadBrandForDelete(ObjectManager $manager)
    {
        $brand = new Brand();
        $brand->setName(self::DELETE_NAME);
        $manager->persist($brand);
        $manager->flush();
    }
}