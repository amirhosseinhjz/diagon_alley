<?php

namespace App\DataFixtures\Product;

use App\DataFixtures\CategoryFixtures;
use App\Entity\Product\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class ProductFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public static function getGroups(): array
    {
        return ['product'];
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadProducts($manager);
    }

    public function loadProducts(ObjectManager $manager): void
    {
//        $leadCategories = (array)$this->getReference(CategoryFixtures::LEAF_CATEGORIES_REFERENCE);
//
//        $product1 = new Product();
//        $product1->setName("product1");
//        $product1->setCategory($leadCategories[0]);
//
//        $product2 = new Product();
//        $product2->setName("product2");
//        $product2->setCategory($leadCategories[0]);
//
//        $manager->persist($product1);
//        $manager->persist($product2);
//        $manager->flush();
    }
}