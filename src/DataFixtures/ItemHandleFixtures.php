<?php

namespace App\DataFixtures;

use App\Entity\Feature\DefineFeature;
use App\Entity\Feature\Feature;
use App\Entity\Feature\ItemValue;
use App\Entity\Variant\Variant;
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
            $feature = new Feature();
            $feature->setLabel("Color$i");
            $feature->setStatus(1);
            $manager->persist($feature);

            $defineFeature = new DefineFeature();
            $defineFeature->setValue("RED$i");
            $defineFeature->setStatus(1);
            $defineFeature->setFeature($feature);
            $manager->persist($defineFeature);

            $variant = new Variant();
            $variant
                ->setStatus(1)
                ->setPrice($i*5)
                ->setDescription("some $i hj")
                ->setQuantity(8*$i+1)
                ->setCreatedAt(new \DateTimeImmutable('now',new \DateTimeZone('Asia/Tehran')))
                ->setSerial(md5($i));
            $manager->persist($variant);

            $itemValue = new ItemValue();
            $itemValue
                ->setFeature($feature)
                ->setValue($defineFeature->getValue())
                ->setVariant($variant);
            $manager->persist($itemValue);
        }
        $manager->flush();
    }

    public function load(ObjectManager $manager)
    {
        $this->loadItemFeature($manager);
    }
}