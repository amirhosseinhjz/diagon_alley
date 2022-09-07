<?php

namespace App\DataFixtures\Cart;

use App\Entity\Cart\Cart;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CartFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $cart = new Cart();
        $cart->setUserId(2);
        $cart->setFinalizedAt(new \DateTimeImmutable());
        $cart->setStatus("INIT");

        $manager->persist($cart);
        $manager->flush();
    }
}
