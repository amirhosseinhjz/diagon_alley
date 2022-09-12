<?php

namespace App\Service\Cart;

use App\DTO\Cart\cartItemDTO;
use App\Entity\Cart\Cart;
use App\Entity\Cart\CartItem;
use App\Entity\Variant\Variant;
use App\Interface\Cart\CartItemServiceInterface;
use App\Interface\Variant\VariantManagementInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class CartItemService implements CartItemServiceInterface
{

    private EntityManagerInterface $entityManager;
    private Serializer $serializer;
    private ValidatorInterface $validator;
    private VariantManagementInterface $variantManagement;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator,
                                VariantManagementInterface $variantManagement
    )
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $this->variantManagement = $variantManagement;
    }

    public function getRequestBody(Request $request)
    {
        return json_decode($request->getContent(), true);
    }

    public function arrayToDTO(array $array)
    {
        return $this->serializer->deserialize(json_encode($array), CartItemDTO::class, 'json');
    }

    private function createValidDTO(array $array)
    {
        $cartItemDTO = $this->arrayToDTO($array);
        $DTOErrors = $this->validateCartItem($cartItemDTO);
        if (count($DTOErrors) > 0) {
            throw (new \Exception(json_encode($DTOErrors)));
        }
        return $cartItemDTO;
    }
    #ToDo: manage relations in dto getters and setters
    public function createCartItemFromDTO(cartItemDTO $dto, bool $flush=true): CartItem
    {
        $item = new CartItem();

        foreach ($dto as $key => $value) {
            $getterName = 'get' . ucfirst($key);
            $setterName = 'set' . ucfirst($key);
            $item->$setterName($dto->$getterName());
        }
        $this->entityManager->persist($item);
        if($flush) {
            $this->entityManager->flush();
        }
        return  $item;
    }

    public function createCartItemFromArray(array $array):CartItem
    {
        $cartItemDTO = $this->createValidDTO($array);
        $item= $this->createCartItemFromDTO($cartItemDTO, false);
        $databaseErrors = $this->validateCartItem($item);
        if (count($databaseErrors) > 0) {
            throw (new \Exception(json_encode($databaseErrors)));
        }
        $this->entityManager->flush();
        return $item;
    }

    public function createDTOFromCartItem(CartItem $item): cartItemDTO
    {
        $cartItemDTO = new cartItemDTO();
        foreach ($item as $key => $value) {
            $getterName = 'get' . ucfirst($key);
            $setterName = 'set' . ucfirst($key);
            $cartItemDTO->$setterName($item->$getterName());
        }
        return $cartItemDTO;
    }

    private function validateCartItem($object): array
    {
        $errors = $this->validator->validate($object);
        $errorsArray = [];
        foreach ($errors as $error) {
            $errorsArray[] = $error->getMessage();
        }
        return $errorsArray;
    }

    function confirmStocksById($itemId, $update = false):bool #check: flush?
    {
        $item = $this->entityManager->getRepository(CartItem::class)->findOneBy(['id'=>$itemId]);
        $stock = $item->getVariant()->getQuantity();
        if($stock < $item->getQuantity()) { #check: !=
            if($update){
                $item->setQuantity($stock);  #ToDo: is this the correct action here?
            }
            return false;
        }
        return true;
    }

    function getCartItemByVariant(Cart $cart, Variant $variant): CartItem
    {
        $item = $this->entityManager->getRepository(CartItem::class)->findOneBy(['cart'=>$cart, 'variant'=>$variant ]);
        if($item === null)
            throw new \Exception("Item does not exist");
        return $item;
    }

    function getCartItemById(int $id): CartItem
    {
        $item = $this->entityManager->getRepository(CartItem::class)->findOneBy(['id'=>$id]);
        if($item === null)
            throw new \Exception("cart item does not exist");
        return $item;
    }

    function removeCartItemById(int $id, $flush = true)
    {
        $item = $this->getCartItemById($id);
        $this->removeCartItem($item);
    }

    function removeCartItem(CartItem $item, $flush = true)
    {
        $item->getCart()->removeItem($item);
        $this->entityManager->remove($item);
        $this->entityManager->flush();
    }

    function updateCartItemById(int $id, int $quantity): CartItem
    {
        $item = $this->getCartItemById($id);
        if($quantity<0){
            throw new \Exception("invalid value for cart item quantity");
        }
        else if($quantity===0){
            $this->removeCartItemById($id, $flush = false);
        }
        else if($item->getVariant()->getQuantity()>=$quantity)
        {
            $item->setQuantity($quantity);
        }
        else{
            throw new \Exception("the requested quantity is not available");
        }
        $this->entityManager->flush();
        return $item;
    }
}