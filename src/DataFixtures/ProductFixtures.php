<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $leadCategories = (array) $this->getReference(CategoryFixtures::LEAF_CATEGORIES_REFERENCE);

        $product1 = new Product();
        $product1->setName("product1");
        $product1->setCategory($leadCategories[0]);

        $product2 = new Product();
        $product2->setName("product2");
        $product2->setCategory($leadCategories[0]);

        $manager->persist($product1);
        $manager->persist($product2);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class
        ];
    }
}