<?php

namespace App\Service\OrderService;

use App\Entity\Order\Purchase;
use App\Entity\Order\PurchaseItem;
use App\Entity\Cart\Cart;
use App\Service\Cart\CartService;
use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    public function __construct(CartService $cartService, EntityManagerInterface $em)
    {
        $this->cartService = $cartService;
        $this->em = $em;
    }

    public function createFromCart(Cart $cart): Purchase
    {
        $purchase = new Purchase();
        $purchase->setCustomer($cart->getUser());
        $purchase->setTotalPrice($cart->getTotalPrice());

        foreach ($cart->getItems() as $cartItem) {
            $purchaseItem = new PurchaseItem();
            $purchaseItem->setVariant($cartItem->getVarient());
            $purchaseItem->setQuantity($cartItem->getCount());
            $purchaseItem->setTotalPrice($cartItem->getTotalPrice());
            $purchaseItem->setPurchase($purchase);

            $purchase->addPurchaseItem($purchaseItem);

        }
        $this->em->persist($purchase);
        $this->em->flush();
        return $purchase;
    }

    public function createOrderByUserId(int $userId): int
    {

        $cart = $this->cartService->getCartByUser($userId);
        if (!$cart) {
            throw new \Exception('Cart not found');
        }
        if ($cart->getItems()->count() === 0) {
            throw new \Exception('Cart is empty');
        }
        $purchase = $this->createFromCart($cart);
        $this->em->persist($purchase);
        $this->cartService->clearCart($cart);
        $this->em->flush();
        return $purchase->getId();
    }
}