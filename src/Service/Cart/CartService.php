<?php

namespace App\Service\Cart;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Exception;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use App\Entity\Cart\Cart;
use App\Entity\Cart\CartItem;
use App\Interface\Cart\CartServiceInterface;
use App\Interface\Cart\CartItemServiceInterface;
use App\DTO\Cart\CartDTO;

/*
public enum Status
{
    case PENDING;
    case INIT;
    case SUCCESS;
    case EXPIRED;
}
*/

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

    public function getRequestBody(Request $req)
    {
        return json_decode($req->getContent(), true);
    }


    private function createDTOFromCart(Cart $cart): CartDTO
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


    public function getCartById(int $cartId)
    {
        $cartRepository = $this->entityManager->getRepository(Cart::class);
        $cart = $cartRepository->findOneBy(['id'=> $cartId, 'status'=>'INIT']); #ToDo? remove init?
        if($cart==null){
            throw new \Exception("Invalid Cart ID");
        }
        return $cart;
    }

    public function resetCart($cartId, $flush = true){
        $cart = $this->getCartById($cartId);
        $cart->setStatus("INIT");
        $cart->setFinalizedAt(null);
        $this->removeAllItems($cart,$flush = false);
        if($flush)
            $this->entityManager->flush();
    }

    private function removeAllItems(Cart $cart, bool $flush = true)
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
        try{
            $cart = $this->getCartById($cartId);
            $total = 0;
            foreach($cart->getItems() as $item)
            {
                #ToDo: get from variant
                $total += $item->getPrice() * $item->getQuantity();
            }
            return $total;
        }
        catch(Exception $exception){
            return ['error' => $exception->getMessage()];
        }
    }

    public function getTotalPrice(Cart $cart)
    {
        try{
            $total = 0;
            foreach($cart->getItems() as $item)
            {
                #ToDo: get from variant
                $total += $item->getPrice() * $item->getQuantity();
            }
            return $total;
        }
        catch(Exception $exception){
            return ['error' => $exception->getMessage()];
        }
    }
    #question do i need this?
    public function removeCart($cart)
    {
        try{
            $this->removeAllItems($cart, $flush = false);
            $this->entityManager->remove($cart);
            $this->entityManager->flush();
        } catch(Exception $exception){
            return ['error' => $exception->getMessage()];
        }
    }

    //T: set up the event for automatic expiration
    public function updateStatus($cartId, $status)
    {
        $cart = $this->getCartById($cartId);
        try{
            switch ($status){
                case "INIT":
                    $cart->setStatus($status);
                    $cart->setFinalizedAt(null);
                    break;
                case "PENDING":
                    $cart->setStatus($status);
                    $cart->setFinalizedAt(new \DateTime("now"));
                    #ToDo: setup automatic exp.
                    #ToDo: should I check item quantity/prices here again?
                    break;
                case "SUCCESS":
                    $cart->setStatus($status);
                    break;
                case "EXPIRED":
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

    public function addItemToCart(array $array)
    { #ToDo change to use cartItemDTO
        if(array_key_exists('variant',$array) && array_key_exists('cartId',$array))  #ToDo: check case: camelCase? add multiple items?
        {
            $cart = $this->getCartById($array['cartId']);
            $item = $this->entityManager->getRepository(CartItem::class)->findOneBy(['id'=>$cart->getId(), 'variant_id'=>$array['variant']['id']]);
            if(!empty($item)){
                $item->increaseQuantity();
                $this->entityManager->flush(); 
            }
            else{
                #get from dtos
                $item = new CartItem();
                $item->setCart($cart);
                $item->setQuantity(1);
                $item->setVariantId($array['variant']['id']);
                $item->setPrice($array['variant']['price']); #t
                $item->setTitle($array['variant']['Title']); #t
                #T #important fill!!! check variant validity
                $cart->addItem($item);
                $this->entityManager->persist($item);
                $this->entityManager->flush();
            }
        }else{
                throw new Exception('insufficient arguments');
        }
    }

    public function removeItemFromCart($array)
    {
        if(array_key_exists('variant',$array) && array_key_exists('cartId',$array))  #camelCase? add multiple items?
        {
            $item = $this->entityManager->getRepository(CartItem::class)->findOneBy(['cartId'=>$array['cartId'], 'variant_id'=>$array['variant']['id']]);
            if(!empty($item)){
                $item->decreaseQuantity();
                if($item->getQuantity() <= 0)
                {
                    $item->getCart()->removeItem($item);
                    $this->entityManager->remove($item);
                }
                $this->entityManager->flush(); 
            }
            else{
                throw new \Exception('Item does not exist');
            }
        }else{
            throw new \Exception('insufficient arguments');
        }

    }

    public function checkItems(Cart $cart, bool $update)
    {
        $flag = true;
        foreach($cart->getItems() as $item){
            $flag = $flag && ($this->cartItemService->checkPrice($item->getId(),$update));
            $flag = $flag && ($this->cartItemService->checkStocks($item->getId(),$update));
        }
        if($update)
            $this->entityManager->flush();
        return $flag;
    }

}