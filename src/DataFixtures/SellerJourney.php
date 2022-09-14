<?php

namespace App\DataFixtures;

use App\Entity\Category\Category;
use App\Entity\Feature\Feature as FeatureEntity;
use App\Entity\Feature\FeatureValue;
use App\Entity\User\Seller;
use App\Entity\Brand\Brand;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SellerJourney extends Fixture implements FixtureGroupInterface
{
    private $passHasher;
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->passHasher = $hasher;
    }

    public static function getGroups(): array
    {
        return ['SellerJourneyFixGroup'];
    }

    private array $categories;
    private array $features;
    private array $featureValues;
    private array $products;
    private array $sellers;
    private array $brands;

    public function load(ObjectManager $manager): void
    {
        //Seller
        for($i = 0; $i < 3; $i++) {
            $seller = new Seller();
            $seller->setShopSlug('myshop' . $i);
            $seller->setPhoneNumber('+9891' . $i . '1234567');
            $seller->setEmail("seller$i@seller.com");
            $seller->setPassword($this->passHasher->hashPassword($seller, '123456789*zZ'));
            $seller->setRoles(['ROLE_SELLER']);
            $seller->setName('seller' . $i);
            $seller->setLastName('seller' . $i . 'lastname');
            $manager->persist($seller);
            $this->sellers[] = $seller;
        }

        //Brand
        for($i = 0; $i < 5; $i++) {
            $brand = new Brand();
            $brand->setName("brand.$i");
            $brand->setDescription("This is cool brand number.$i");
            $manager->persist($brand);
            $this->brands[] = $brand;
        }

        //Feature
        for($i = 0; $i < 10; $i++) {
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

        //Category
        $manager->flush();
    }
}
