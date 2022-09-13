<?php

namespace App\Service\OrderService;

use App\Interface\Order\OrderManagementInterface;
use App\Entity\Order\Purchase;
use App\Entity\Order\PurchaseItem;
use App\Entity\Cart\Cart;
use App\Service\Cart\CartService;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Address\Address;
use App\Service\Address\AddressService;

class OrderService implements OrderManagementInterface
{
    public function __construct(CartService $cartService, AddressService $addressService, EntityManagerInterface $em)
    {
        $this->cartService = $cartService;
        $this->addressService = $addressService;
        $this->em = $em;
    }

    public function createFromCart(Cart $cart): Purchase
    {
        $purchase = new Purchase();
        $purchase->setCustomer($cart->getUser()); # TODO: safa
        $purchase->setTotalPrice($cart->getTotalPrice()); # TODO: safa
        $this->setOrderItems($purchase, $cart);
        return $purchase;
    }

    private function setOrderItems(Purchase $purchase, Cart $cart)
    {
        $this->em->getConnection()->beginTransaction();
        try{
        foreach ($cart->getItems() as $cartItem) {
            $quantity = $cartItem->getCount();
            $variant = $cartItem->getVariant();
            if ($variant->getQuantity() < $quantity) {
                throw new \Exception('Not enough items of '.$variant->getSerial().' in stock');
            }
            $variant->setQuantity($variant->getQuantity() - $quantity);
            $purchaseItem = new PurchaseItem();
            $purchaseItem->setVariant($variant);
            $purchaseItem->setQuantity($quantity);
            $purchaseItem->setTotalPrice($cartItem->getTotalPrice());
            $purchaseItem->setPurchase($purchase);

            $purchase->addPurchaseItem($purchaseItem);
            $this->em->persist($purchaseItem);
        }
        $this->em->flush();
        $this->em->getConnection()->commit();
        }
        catch(\Exception $e){
            $this->em->getConnection()->rollBack();
            throw $e;
        }
    }

    public function createOrderByCartId(int $cartId, int $addressId): int
    {

        $cart = $this->cartService->getCartById($cartId);
        if (!$cart) {
            throw new \Exception('Cart not found');
        }
        $address = $this->em->getRepository(Address::class)->find($addressId);
        if (!$address) {
            throw new \Exception('Address not found');
        }
        if ($address->getUser() != $cart->getUser()) {
            throw new \Exception('Address does not belong to user');
        }
        if ($cart->getItems()->count() === 0) {
            throw new \Exception('Cart is empty');
        }
        $purchase = $this->createFromCart($cart);
        $purchase->setAddress($address);
        $purchase->setStatus($purchase::STATUS_PENDING);
        $this->em->persist($purchase);
        $this->cartService->clearCart($cart); #TODO: safa
        $this->em->flush();
        return $purchase->getId();
    }

    public function submitOrder(array $params): int
    {
        if (!isset($params['cartId']) || !isset($params['addressId'])) {
            throw new \Exception('Invalid params');
        }
        $cartId = $params['cartId'];
        $addressId = $params['addressId'];
        return $this->createOrderByCartId($cartId, $addressId);
    }

    public function getOrderById(int $orderId): Purchase
    {
        $order = $this->em->getRepository(Purchase::class)->find($orderId);
        if (!$order) {
            throw new \Exception('Order not found');
        }
        return $order;
    }

    public function finalizeOrder($orderId): void
    {
        $order = $this->getOrderById($orderId);
        $order->setStatus($order::STATUS_PAID);
        $this->em->flush();
//        TODO: call shipping service
    }

    //returns the sum of item prices without any discount or change
    public function rawTotalPrice(Purchase $purchase){
        $price = 0;
        foreach ($purchase->getPurchaseItems() as $item){
            $price += $item->getVariant()->getPrice() * $item->getQuantity();
        }
        return $price;
    }

}