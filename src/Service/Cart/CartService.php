<?php

namespace App\Service\Cart;

use App\Entity\Variant\Variant;
use App\Service\VariantService\VariantManagement;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\HttpFoundation\Request;
use Exception;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use App\Entity\Cart\Cart;
use App\Entity\Cart\CartItem;
use App\Entity\User\Customer;
use App\Interface\Cart\CartServiceInterface;


class CartService implements CartServiceInterface
{

    private EntityManagerInterface $entityManager;
    private $serializer;
    private $validator;

    public function __construct(EntityManagerInterface $entityManager,
                                ValidatorInterface $validator,
                                VariantManagement $variantManagement
    )
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $this->variantManagement = $variantManagement;
    }

    private function isEditable(Cart $cart)
    {
        if (!$cart->getStatus() == Cart::STATUS_INIT) {
            throw new Exception("Cart is not editable");
        }
    }

    private function validateRequest($params, $requestBody)
    {
        foreach ($params as $param) {
            if (!array_key_exists($param, $requestBody)) {
                throw new \Exception("Invalid request parameters");
            }
        }
    }

    public function getCartById(int $cartId):Cart
    {
        $cartRepository = $this->entityManager->getRepository(Cart::class);
        $cart = $cartRepository->find($cartId);
        if(!$cart){
            throw new \Exception("Invalid Cart ID");
        }
        return $cart;
    }

    public function createCart(Customer $user):Cart
    {
        $cart = new Cart();
        $cart->setCustomer($user);
        $cart->setStatus(Cart::STATUS_INIT);
        $this->entityManager->persist($cart);
        $this->entityManager->flush();
        return $cart;
    }

    public function addToCartFromRequest(array $requestBody):Cart
    {
        $params = ['cartId', 'variantId', 'quantity'];
        $this->validateRequest($params, $requestBody);
        foreach ($requestBody as $key => $value) {
            $$key = $value;
        }
        return $this->addToCartById($cartId, $variantId, $quantity);
    }

    public function removeFromCartFromRequest(array $requestBody):Cart
    {
        $params = ['cartId', 'cartItemId', 'quantity'];
        $this->validateRequest($params, $requestBody);
        foreach ($requestBody as $key => $value) {
            $$key = $value;
        }
        return $this->removeFromCartById($cartId, $cartItemId, $quantity);
    }

    public function addToCartById(int $cartId, int $variantId, int $quantity):Cart
    {
        $cart = $this->getCartById($cartId);
        $this->isEditable($cart);
        $variant = $this->variantManagement->getById($variantId);
        $item = $this->addItem($cart, $variant, $quantity);
        return $cart;
    }

    private function addItem(Cart $cart, Variant $variant, int $quantity):CartItem
    {
        if ($variant->getQuantity() < $quantity) {
            throw new \Exception("Not enough stock");
        }
        foreach ($cart->getItems() as $item){
            if($item->getVariant()->getId() == $variant->getId()){
                $item->increaseQuantity($quantity);
                $this->entityManager->persist($item);
                $this->entityManager->flush();
                return $item;
            }
        }
        return $this->addNewItem($cart, $variant, $quantity);
    }

    private function addNewItem(Cart $cart, Variant $variant, int $quantity):CartItem
    {
        $item = new CartItem();
        $item->setVariant($variant);
        $item->setQuantity($quantity);
        $item->setCart($cart);
        $cart->addItem($item);
        $this->entityManager->persist($item);
        $this->entityManager->persist($cart);
        $this->entityManager->flush();
        return $item;
    }

    public function removeFromCartById(int $cartId, int $itemId, ?int $quantity=null):Cart
    {
        $cart = $this->getCartById($cartId);
        $this->isEditable($cart);
        $item = $this->cartItemService->getCartItemById($itemId);
        if ($item->getCart()->getId() != $cart->getId()) {
            throw new \Exception("Invalid Item");
        }
        $this->decreaseItem($cart, $item, $quantity);
        return $cart;
    }

    private function decreaseItem(Cart $cart, CartItem $item, ?int $quantity):CartItem
    {
        if (!$quantity || $item->getQuantity() == $quantity) {
            $this->removeItem($cart, $item);
        }
        return $this->decreaseItemQuantity($item, $quantity);
    }

    private function removeItem(Cart $cart, CartItem $item):CartItem
    {
        $cart->removeItem($item);
        $this->entityManager->remove($item);
        $this->entityManager->persist($cart);
        $this->entityManager->flush();
        return $item;
    }

    private function decreaseItemQuantity(CartItem $item, int $quantity):CartItem
    {
        if($item->getQuantity() < $quantity){
            throw new \Exception("Invalid quantity");
        }
        $item->decreaseQuantity($quantity);
        $this->entityManager->persist($item);
        $this->entityManager->flush();
        return $item;
    }

    public function resetCart($cartId, $flush = true):Cart{
        $cart = $this->getCartById($cartId);
        $cart->setStatus(Cart::STATUS_INIT);
        $cart->setFinalizedAt(null);
        $this->clearCart($cart,$flush = false);
        if($flush)
            $this->entityManager->flush();
        return $cart;
    }

    private function clearCart(Cart $cart)
    {
        foreach ($cart->getItems() as $item) {
            $this->removeItem($cart, $item);
        }
    }

    public function clearCartById(int $cartId)
    {
        $cart = $this->getCartById($cartId);
        $this->isEditable($cart);
        $this->clearCart($cart);
        return $cart;
    }

    public function clearCartFromRequest(array $requestBody):Cart
    {
        $params = ['cartId'];
        $this->validateRequest($params, $requestBody);
        foreach ($requestBody as $key => $value) {
            $$key = $value;
        }
        return $this->clearCartById($cartId);
    }

    public function expireCart(Cart $cart)
    {
        $this->isEditable($cart);
        $cart->setStatus(Cart::STATUS_EXPIRED);
        $this->entityManager->persist($cart);
        $this->entityManager->flush();
        return $cart;
    }
}