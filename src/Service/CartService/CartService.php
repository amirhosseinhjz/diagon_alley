<?php

namespace App\Service\CartService;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Exception;
use App\Entity\Cart\Cart;
use App\Entity\Cart\CartItem;
use App\Interface\Cart\CartServiceInterface;

class CartService implements CartServiceInterface
{
    private EntityManagerInterface $entityManager;

    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getRequestBody(Request $req)
    {
        return json_decode($req->getContent(), true);
    }

    public function validateCartArray(array $unvalidatedArray)
    {
        try {
            if(!array_key_exists("user_id",$unvalidatedArray)) 
                throw new Exception("user_id is required"); 
            $user_id = $unvalidatedArray["user_id"];
            #t: one item or more? what to do is items is empty?
            #$items = array_key_exists("items",$unvalidatedArray)?$unvalidatedArray["items"]:array();
            return ["user_id"=> $user_id, /*'items' => $items ,*/ "status" => 'init'];
        } catch(Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    public function getCart(int $userId, bool $create = true)
    {
        try{
            $cartRepository = $this->entityManager->getRepository(Cart::class);
            $cart = $cartRepository->findOneBy(['user_id'=> $userId, 'status'=>'init']); #check: is this a cart? should this be here?just init?

            if($cart==null && $create){  #T: check
                $cart = new Cart();
                $cart->setUserId($userId);
                $cart->setStatus('init');
                $this->entityManager->persist($cart);
                $this->entityManager->flush();
            }
            return $cart;
        } catch(Exception $exception){
            return ['error' => $exception->getMessage()];
        }         
    }

    public function getCartById(int $cartId)
    {
        try{
            $cartRepository = $this->entityManager->getRepository(Cart::class);
            $cart = $cartRepository->findOneBy(['id'=> $cartId, 'status'=>'init']);
            if($cart==null){
                #T ?
            }
            return $cart;
        } catch(Exception $exception){
            #T?
        }
    }


    public function getTotalPrice($cartId)
    {
        try{
            $cartRepository = $this->entityManager->getRepository(Cart::class);
            $cart = $cartRepository->findOneBy(['id'=>$cartId]); 
            //$this->security->denyAccessUnlessGranted('view',$cart);
            $total = 0;
            foreach($cart->getItems() as $item)
            {
                $total += $item->getPrice() * $item->getCount();
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
            #T: remove all cart items?? isn't it automatically done?
            $this->entityManager->remove($cart);
            $this->entityManager->flush();
        } catch(Exception $exception){
            return ['error' => $exception->getMessage()];
        }
    }

    //T: set up the event for automatic expiration
    public function updateStatus($cartId, $status)
    {
        $cart = $this->entityManager->getRepository(Cart::class)->findOneBy(['id'=>$cartId]);
        try{
            //T: if cart does not exist? if cart is empty?
            $cart->setStatus($status);
            if($status === "PENDING")  #t: define constants
            {
                $cart->setFinalizedAt(new \DateTime("now")); 
                #T: setup automatic exp.
            }

            $this->entityManager.flush();

        } catch(Exception $exception){
            return ['error' => $exception->getMessage()];
        }
    }

    public function addItemToCart(array $array)
    {
        if(array_key_exists('varient',$array) && array_key_exists('userid',$array))  #camelCase? add multiple items?
        {
            $cart = $this->getCart($array['userid']);
            $item = $this->entityManager->getRepository(CartItem::class)->findOneBy(['id'=>$cart->getId(), 'varient_id'=>$array['varient']['id']]);
            if(!empty($item)){
                $item->increaseCount();
                $this->entityManager->flush(); 
            }
            else{
                #get from dtos
                $item = new CartItem();
                $item->setCart($cart);
                $item->setCartId($cart->getId());
                $item->setCount(1);
                $item->setVarientId($array['varient']['id']);
                $item->setPrice($array['varient']['price']); #t
                $item->setTitle($array['varient']['Title']); #t
                #T #important fill!!! check varient validity
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
        if(array_key_exists('varient',$array) && array_key_exists('userid',$array))  #camelCase? add multiple items?
        {
            $cart = $this->getCart($array['userid']);
            $item = $this->entityManager->getRepository(CartItem::class)->findOneBy(['id'=>$cart->getId(), 'varient_id'=>$array['varient']['id']]);
            if(!empty($item)){
                $item->decreaseCount();
                if($item->getCount() <= 0)
                {
                    $item->getCart()->removeItem($item);
                    $this->entityManager->remove($item);
                }
                $this->entityManager->flush(); 
            }
            else{
                throw new Exception('Item does not exist');
            }
        }else{
                throw new Exception('insufficient arguments');
        }

    }
    
}