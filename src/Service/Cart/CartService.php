<?php

namespace App\Service\Cart;

use App\DataFixtures\Item\ItemHandleFixtures;
use App\DTO\Cart\CartItemDTO;
use App\DTO\Cart\CartDTO;
use App\Entity\Variant\Variant;
use App\Service\VariantService\VariantManagement;
use Doctrine\ORM\EntityManagerInterface;
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
use App\Interface\Cart\CartItemServiceInterface;


class CartService implements CartServiceInterface
{

    private EntityManagerInterface $entityManager;
    private CartItemServiceInterface $cartItemService;
    private $serializer;
    private $validator;
    
    public function __construct(EntityManagerInterface $entityManager,
                                ValidatorInterface $validator,
                                CartItemServiceInterface $cartItemService,
                                VariantManagement $variantManagement
    )
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $this->cartItemService = $cartItemService;
        $this->variantManagement = $variantManagement;
    }

    private function isEditable(Cart $cart)
    {
        if (!$cart->getStatus() == Cart::STATUS_INIT) {
            throw new Exception("Cart is not editable");
        }
    }

    public function getRequestBody(Request $request)
    {
        return json_decode($request->getContent(), true);
    }

    public function createDTOFromCart(Cart $cart): CartDTO
    {
        $cartDTO = new CartDTO();
        $cartDTO->items = array();
        foreach($cart->getItems() as $item){
            $cartDTO->items[] = $this->cartItemService->createDTOFromCartItem($item);
        }
        foreach ($cart as $key => $value) {
            if($key == "items")
                continue;
            $getterName = 'get' . ucfirst($key);
            $setterName = 'set' . ucfirst($key);
            $cartDTO->$setterName($cart->$getterName());
        }
        return $cartDTO;
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
        $cart->setCustomer($user);  #ToDo: validate the user
        $cart->setStatus(Cart::STATUS_INIT);
        $this->entityManager->persist($cart);
        $this->entityManager->flush();
        return $cart;
    }

    public function addToCartFromRequest(array $requestBody):Cart
    {
        $params = ['cartId', 'variantId', 'quantity'];
        foreach ($params as $param) {
            if (!array_key_exists($param, $requestBody)) {
                throw new \Exception("Invalid request parameters");
            }
            $$param = $requestBody[$param];
        }
        return $this->addToCartById($cartId, $variantId, $quantity);
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
                $item->increaseQuantity();
                $this->entityManager->persist($item);
                $this->entityManager->flush();
                return $item;
            }
        }
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

    private function clearCart(Cart $cart, bool $flush = true)
    {
        foreach($cart->getItems() as $item){
            $this->entityManager->remove($item);
            $cart->removeItem($item);
        }
        if($flush)
            $this->entityManager->flush();
    }

    public function getTotalPriceById($cartId)
    {
            $cart = $this->getCartById($cartId);
            return $this->getTotalPrice($cart);
    }

    public function getTotalPrice(Cart $cart)
    {
        try{
            $total = 0;
            foreach($cart->getItems() as $item)
            {
                $total += $item->getVariant()->getPrice() * $item->getQuantity();
            }
            return $total;
        }
        catch(Exception $exception){
            return ['error' => $exception->getMessage()];
        }
    }

    public function expireCart(int $cartId)
    {
        $cart = $this->getCartById($cartId);
        $this->isEditable($cart);
        $cart->setStatus(Cart::STATUS_EXPIRED);
        $this->entityManager->persist($cart);
        $this->entityManager->flush();
        return $cart;
    }

    public function removeCart($cart)
    {
        try{
            $this->clearCart($cart, $flush = false);
            $this->entityManager->remove($cart);
            $this->entityManager->flush();
        } catch(Exception $exception){
            return ['error' => $exception->getMessage()];
        }
    }

    public function updateStatus($cartId, $status)
    {
        $cart = $this->getCartById($cartId);
        try{
            switch ($status){
                case Cart::STATUS_INIT:
                    $cart->setStatus($status);
                    $cart->setFinalizedAt(null);
                    break;
                case Cart::STATUS_PENDING:
                    $cart->setStatus($status);
                    if($this->confirmItems($cart)) {
                        $cart->setFinalizedAt(new \DateTime("now"));
                        $cart->setStatus(Cart::STATUS_PENDING);
                    }
                    else {
                        throw new \Exception("some items are not available in the requested quantity");
                    }
                    break;
                case Cart::STATUS_SUCCESS:
                    $cart->setStatus($status);
                    break;
                case Cart::STATUS_EXPIRED:
                    $cart->setStatus($status);
                    break;
                default:
                    throw new \Exception("Invalid Status Code");
            }
            $this->entityManager.flush();

        } catch(Exception $exception){
            return ['error' => $exception->getMessage()];
        }
    }

    #ToDo: return the list of problematic items and their available quantities
    public function confirmItems(Cart $cart, bool $update = false):bool
    {
        $flag = true;
        foreach($cart->getItems() as $item){
            $flag = $flag && ($this->cartItemService->confirmStocksById($item->getId(),$update));
        }
        if($update)
            $this->entityManager->flush();
        return $flag;
    }

    public function updateCartFromDTO(CartDTO $dto): Cart
    {
        $cart = $this->getCartById($dto->getId());
        $cart->setStatus($dto->getStatus());
        foreach ($dto as $dtoItem){
            $cart->addItem($this->cartItemService->createCartItemFromArray($dtoItem));
        }
        #check: remove items?
        $this->entityManager->flush();
        return $cart;
    }

    public function arrayToDTO(array $array)
    {
        return $this->serializer->deserialize(json_encode($array), CartItemDTO::class, 'json');
    }

    private function createValidDTO(array $array)
    {
        $cartDTO = $this->arrayToDTO($array);
        $DTOErrors = $this->validateCart($cartDTO);
        if (count($DTOErrors) > 0) {
            throw (new \Exception(json_encode($DTOErrors)));
        }
        return $cartDTO;
    }

    private function validateCart($object): array
    {
        $errors = $this->validator->validate($object);
        $errorsArray = [];
        foreach ($errors as $error) {
            $errorsArray[] = $error->getMessage();
        }
        return $errorsArray;
    }

}