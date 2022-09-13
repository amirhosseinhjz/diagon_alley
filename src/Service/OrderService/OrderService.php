<?php

namespace App\Service\OrderService;

use App\Entity\Address\Address;
use App\Entity\Cart\Cart;
use App\Entity\Order\Purchase;
use App\Entity\Order\PurchaseItem;
use App\Entity\User\Customer;
use App\Interface\Order\OrderManagementInterface;
use App\Service\Cart\CartService;
use Doctrine\ORM\EntityManagerInterface;
//use App\Interface\Shipment\ShipmentManagementInterface;

class OrderService implements OrderManagementInterface
{
    public function __construct(
        EntityManagerInterface $em,
        CartService $cartService,
//        ShipmentManagementInterface $shipmentService,
    )
    {
        $this->em = $em;
        $this->cartService = $cartService;
//        $this->shipmentService = $shipmentService;
    }

    public function createFromCart(Cart $cart): Purchase
    {
        $purchase = new Purchase();
        $purchase->setCustomer($cart->getCustomer());
        $purchase->setTotalPrice($cart->getTotalPrice());
        $this->setOrderItems($purchase, $cart);
        return $purchase;
    }

    public function getCustomerOrders(Customer $customer): array
    {
        return $this->em->getRepository(Purchase::class)->findBy(['customer' => $customer]);
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
        if ($address->getUser() != $cart->getCustomer()) {
            throw new \Exception('Address does not belong to user');
        }
        if ($cart->getItems()->count() === 0) {
            throw new \Exception('Cart is empty');
        }
        $purchase = $this->createFromCart($cart);
        $purchase->setAddress($address);
        $purchase->setStatus($purchase::STATUS_PENDING);
        $this->em->persist($purchase);
        $this->cartService->clearCart($cart);
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

    public function finalizeOrder(Purchase $order): void
    {
        $order->setStatus($order::STATUS_PAID);
        $this->em->flush();
        $this->shipmentService->add($order->getId());
    }

    public function cancelOrderItemById(Purchase $purchase, int $orderItemId): int
    {
        $orderItem = $this->em->getRepository(PurchaseItem::class)->find($orderItemId);
        if (!$orderItem) {
            throw new \Exception('Order item not found');
        }
        if ($orderItem->getPurchase() != $purchase) {
            throw new \Exception('Order item does not belong to order');
        }
        return $this->cancelOrderItem($orderItem);
    }

    public function cancelOrderItem(PurchaseItem $orderItem, bool $flush=false): int
    {
        $orderItem->setStatus($orderItem::STATUS_CANCELED);
        $price = $orderItem->getTotalPrice();
//        call wallet service
        $orderItem->getVariant()->increaseQuantity($orderItem->getQuantity());
        if ($flush) {
            $this->em->flush();
        }
        return $orderItem->getTotalPrice();
    }

    public function cancelOrderItemByIds(int $orderId, int $orderItemId): int
    {
        $order = $this->getOrderById($orderId);
        $order->isCancellable();
        return $this->cancelOrderItemById($order, $orderItemId);
    }

    public function cancelOrderById(int $orderId): int
    {
        $order = $this->getOrderById($orderId);
        return $this->cancelOrder($order);
    }

    public function cancelOrder(Purchase $order): int
    {
        $order->isCancellable();
        foreach ($order->getPurchaseItems() as $orderItem) {
            $this->cancelOrderItem($orderItem);
        }
        return 0;
    }

    public function getPurchaseItemsBySellerIdAndPurchaseId(array $criteria)
    {
        return $this->em->getRepository(PurchaseItem::class)->findBySellerIdAndPurchaseId($criteria);
    }

    public function getPurchaseItemById($id)
    {
        return $this->em->getRepository(PurchaseItem::class)->findOneBy(['id'=>$id]);
    }
}