<?php

namespace App\DataFixtures\Feature;

use App\Entity\Feature\FeatureValue;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Feature\Feature as FeatureEntity;

class Feature extends Fixture implements FixtureGroupInterface
{
    public const FEATURE = 'feature';

    public static function getGroups(): array
    {
        return ['FeatureFixGroup'];
    }

    public function loadFeature(ObjectManager $manager)
    {
        for ($i = 0; $i < 7; $i++) {
            $feature = new FeatureEntity();
            $feature->setLabel("Color$i");
            $feature->setActive(1);
            $manager->persist($feature);

            $featureValue = new FeatureValue();
            $featureValue->setValue("RED$i");
            $featureValue->setActive(1);
            $featureValue->setFeature($feature);
            $manager->persist($featureValue);
        }
        $manager->flush();
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadFeature($manager);
    }
}
