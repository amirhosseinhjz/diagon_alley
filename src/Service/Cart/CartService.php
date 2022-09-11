<?php

namespace App\Service\Cart;

use App\DTO\Cart\CartItemDTO;
use App\DTO\Cart\CartDTO;
use Doctrine\ORM\EntityManagerInterface;
use http\Env\Response;
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
                                CartItemServiceInterface $cartItemService)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $this->cartItemService = $cartItemService;
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
        $cart = $cartRepository->findOneBy(['id'=> $cartId]);
        if($cart==null){
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

    public function resetCart($cartId, $flush = true):Cart{
        $cart = $this->getCartById($cartId);
        $cart->setStatus(Cart::STATUS_INIT);
        $cart->setFinalizedAt(null);
        $this->clearCart($cart,$flush = false);
        if($flush)
            $this->entityManager->flush();
        return $cart;
    }

    public function clearCart(Cart $cart, bool $flush = true)
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