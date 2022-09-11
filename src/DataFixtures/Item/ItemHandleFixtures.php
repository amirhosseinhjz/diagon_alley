<?php

namespace App\DataFixtures\Item;

use App\DataFixtures\User\UserFixtures;
use App\Entity\Feature\FeatureValue;
use App\Entity\Feature\Feature;
use App\Entity\Variant\Variant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ItemHandleFixtures extends Fixture implements FixtureGroupInterface , DependentFixtureInterface
{
    public static function getGroups(): array
    {
        return ['ItemFixGroup'];
    }

    public function loadItemFeature(ObjectManager $manager)
    {
        //$sellers = (array) $this->getReference(UserFixtures::SELLER);
        $sellers =[];

        for ($i = 0; $i < 4; $i++) {
            $feature = new Feature();
            $feature->setLabel("Color$i");
            $feature->setActive(1);
            $manager->persist($feature);

            $featureValue = new FeatureValue();
            $featureValue->setValue("RED$i");
            $featureValue->setActive(1);
            $featureValue->setFeature($feature);
            $manager->persist($featureValue);

            $variant = new Variant();
            $variant
                ->setValid(1)
                ->setPrice($i*5)
                ->setDescription("some $i hj")
                ->setQuantity(8*$i+1)
                ->setCreatedAt(new \DateTimeImmutable('now',new \DateTimeZone('Asia/Tehran')))
                ->setSerial(md5($i))
                ->setSoldNumber(0)
                ->setType('physical')
                ->setSeller($this->getReference('seller'))
                ->setSeller($sellers[$i % 2])
                ->addFeatureValue($featureValue);
            $manager->persist($variant);
        }
        $manager->flush();
    }

    public function load(ObjectManager $manager)
    {
        // $this->loadItemFeature($manager);
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }
}