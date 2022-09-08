<?php

namespace App\DataFixtures\Item;

use App\Entity\Feature\FeatureValue;
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
        for ($i = 0; $i < 4; $i++) {
            $feature = new Feature();
            $feature->setLabel("Color$i");
            $feature->setStatus(1);
            $manager->persist($feature);

            $featureValue = new FeatureValue();
            $featureValue->setValue("RED$i");
            $featureValue->setStatus(1);
            $featureValue->setFeature($feature);
            $manager->persist($featureValue);

            $variant = new Variant();
            $variant
                ->setStatus(1)
                ->setPrice($i*5)
                ->setDescription("some $i hj")
                ->setQuantity(8*$i+1)
                ->setCreatedAt(new \DateTimeImmutable('now',new \DateTimeZone('Asia/Tehran')))
                ->setSerial(md5($i))
                ->setSoldNumber(0)
                ->addFeatureValue($featureValue);
            $manager->persist($variant);
        }
        $manager->flush();
    }

    public function load(ObjectManager $manager)
    {
        // $this->loadItemFeature($manager);
    }
}