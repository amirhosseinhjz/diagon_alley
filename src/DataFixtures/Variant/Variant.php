<?php

namespace App\DataFixtures\Variant;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class Variant extends Fixture
{
    public function loadVariant(ObjectManager $manager)
    {
        //$sellers = (array) $this->getReference(UserFixtures::SELLER);
        $sellers =[];

        for ($i = 0; $i < 4; $i++) {
            $variant = new \App\Entity\Variant\Variant();
//            $variant
//                ->setValid(1)
//                ->setPrice($i*5)
//                ->setDescription("some $i hj")
//                ->setQuantity(8*$i+1)
//                ->setCreatedAt(new \DateTimeImmutable('now',new \DateTimeZone('Asia/Tehran')))
//                ->setSerial(md5($i))
//                ->setSoldNumber(0)
//                ->setType('physical')
//                ->setSeller($this->getReference('seller'))
//                ->setSeller($sellers[$i % 2])
//                ->addFeatureValue($featureValue);
            $manager->persist($variant);
        }
        $manager->flush();
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadVariant($manager);
    }
}
