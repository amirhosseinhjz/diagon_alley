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

    public function arrayToDTO(array $array)
    {
        return $this->serializer->deserialize(json_encode($array), CartDTO::class, 'json');
    }

    private function createValidDTO(array $array)
    {
        $cartDTO = $this->arrayToDTO($array);
        $DTOErrors = $this->validate($cartDTO);
        if (count($DTOErrors) > 0) {
            throw (new \Exception(json_encode($DTOErrors)));
        }
        return $cartDTO;
    }

    #not useful for now
    public function createFromDTO(CartDTO $dto, bool $flush=true): Cart
    {
        $cart = new Cart();

        $cart->setStatus($dto -> getStatus()); #Todo: make the constants
        $cart->setUserId($dto -> getUserId());

        $this->entityManager->persist($cart);
        if($flush) {
            $this->entityManager->flush();
        }
        #Todo: complete this method
        return  $cart;
    }

    public function createFromArray(array $array) : Cart

    {
        $cartDTO = $this->createValidDTO($array);
        $cart = $this->createFromDTO($cartDTO, false);
        $databaseErrors = $this->validate($cart);
        if (count($databaseErrors) > 0) {
            throw (new \Exception(json_encode($databaseErrors)));
        }
        $this->entityManager->flush();
        return $cart;
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

    private function validate($object): array
    {
        $errors = $this->validator->validate($object);
        $errorsArray = [];
        foreach ($errors as $error) {
            $errorsArray[] = $error->getMessage();
        }
        return $errorsArray;
    }


    public function getCartByUser(int $userId, bool $create = true)
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
            throw new \Exception("unable to create cart");
        }
    }

    public function getCartById(int $cartId)
    {

        $cartRepository = $this->entityManager->getRepository(Cart::class);
        $cart = $cartRepository->findOneBy(['id'=> $cartId, 'status'=>'init']);
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
    {
        if(array_key_exists('varient',$array) && array_key_exists('userid',$array))  #camelCase? add multiple items?
        {
            $cart = $this->getCartByUser($array['userid']);
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
            $cart = $this->getCartByUser($array['userid']);
            $item = $this->entityManager->getRepository(CartItem::class)->findOneBy(['Cart_Id'=>$cart->getId(), 'varient_id'=>$array['varient']['id']]);
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

    #check: useless?
    public function getCartId(int $userId)
    {
        $cart = $this->getCartByUser($userId);
        return $cart->getId();
    }
}