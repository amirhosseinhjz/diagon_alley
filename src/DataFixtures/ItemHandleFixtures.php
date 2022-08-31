<?php

namespace App\DataFixtures;

use App\Entity\ProductItem\ItemFeature;
use App\Entity\ProductItem\DefineFeature;
use App\Entity\ProductItem\ItemValue;
use App\Entity\ProductItem\Varient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class ItemHandleFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['ItemHandle'];
    }

    public function loadItemFeature(ObjectManager $manager)
    {
        for ($i = 0; $i < 3; $i++) {
            $itemFeature = new ItemFeature();
            $itemFeature->setLabel("Color$i");
            $itemFeature->setStatus(1);
            $manager->persist($itemFeature);

            $defineFeature = new DefineFeature();
            $defineFeature->setValue("RED$i");
            $defineFeature->setStatus(1);
            $defineFeature->setItemFeature($itemFeature);
            $manager->persist($defineFeature);

            $varient = new Varient();
            $varient
                ->setStatus(1)
                ->setPrice($i*5)
                ->setDescription("some $i hj")
                ->setQuantity(8*$i+1)
                ->setCreatedAt(new \DateTimeImmutable('now',new \DateTimeZone('Asia/Tehran')))
                ->setSerial(md5($i));
            $manager->persist($varient);

            $itemValue = new ItemValue();
            $itemValue
                ->setItemFeature($itemFeature)
                ->setValue($defineFeature->getValue())
                ->setVarient($varient);
            $manager->persist($itemValue);
        }
        $manager->flush();
    }

    public function load(ObjectManager $manager)
    {
        $this->loadItemFeature($manager);
    }
}