<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public const LEAF_CATEGORIES_REFERENCE = 'leafCategories';

    public function load(ObjectManager $manager): void
    {
        //TODO: replace with this->createMany in BaseFixture

        $mainCategories = [];
        for ($i = 1; $i < 6; $i++) {
            $mainCategory = new Category();
            $mainCategory->setName("mainCategory" . "$i");
            $manager->persist($mainCategory);
            $mainCategories[] = $mainCategory;
        }
        $manager->flush();

        $middleCategories = [];
        foreach ($mainCategories as $mainCategory) {
            for ($i = 1; $i < 4; $i++) {
                $middleCategory = new Category();
                $middleCategory->setName("middleCategory" . "$i");
                $middleCategory->setParent($mainCategory);
                $manager->persist($middleCategory);
                $middleCategories[] = $middleCategory;
            }
        }
        $manager->flush();

        $leafCategories = [];
        foreach ($middleCategories as $middleCategory) {
            for ($i = 1; $i < 3; $i++) {
                $leafCategory = new Category();
                $leafCategory->setName("leafCategory" . "$i");
                $leafCategory->setParent($middleCategory);
                $leafCategory->setIsLeaf(true);
                $manager->persist($leafCategory);
                $leafCategories[] = $leafCategory;
            }
        }
        $manager->flush();

        $this->addReference(self::LEAF_CATEGORIES_REFERENCE, (object)$leafCategories);
    }
}
