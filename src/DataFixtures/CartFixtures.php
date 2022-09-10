<?php

namespace App\DataFixtures;

use App\Entity\Cart\Cart;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
#ToDo: fill and create items
class CartFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $cart = new Cart();
        $cart->setUser(null);
        $cart->setFinalizedAt(new \DateTimeImmutable());
        $cart->setStatus("INIT");

        $manager->persist($cart);
        $manager->flush();
    }
}
