<?php

namespace App\Service\Cart;

use App\DTO\Cart\cartItemDTO;
use App\Entity\Cart\Cart;
use App\Entity\Cart\CartItem;
use App\Interface\Cart\CartItemServiceInterface;

use App\Repository\ProductItem\VarientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;



#todo: change
use App\Service\VarientService\VarientManagement;

class CartItemService implements CartItemServiceInterface
{

    private EntityManagerInterface $entityManager;
    private $serializer;
    private $validator;
    #todo change
    private $vm;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator,
    )
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        #todo: change
    }

    public function arrayToDTO(array $array)
    {
        return $this->serializer->deserialize(json_encode($array), CartItemDTO::class, 'json');
    }

    private function createValidDTO(array $array)
    {
        $cartItemDTO = $this->arrayToDTO($array);
        $DTOErrors = $this->validate($cartItemDTO);
        if (count($DTOErrors) > 0) {
            throw (new \Exception(json_encode($DTOErrors)));
        }
        return $cartItemDTO;
    }

    public function createFromDTO(cartItemDTO $dto, bool $flush=true): CartItem
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


    public function createFromArray(array $array) : Cart

    {
        $cartItemDTO = $this->createValidDTO($array);
        $item= $this->createFromDTO($cartItemDTO, false);
        $databaseErrors = $this->validate($item);
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

    private function validate($object): array
    {
        $errors = $this->validator->validate($object);
        $errorsArray = [];
        foreach ($errors as $error) {
            $errorsArray[] = $error->getMessage();
        }
        return $errorsArray;
    }


    function checkStocks($itemId, $update = false) #check: flush?
    {
        $item = $this->entityManager->getRepository(CartItem::class)->findOneBy(['id'=>$itemId]);
        $stock = $this->vm->getVarientStock($item->getVarientId(),$this->entityManager->getRepository(VarientRepository::class));
        if($stock < $item->getCount()) { #check: !=
            if($update){
                $item->setCount($stock);  #ToDo: is this the correct action here?
            }
            return false;
        }
        return true;
    }


    function checkPrice($itemId ,$update = false) #check: flush?
    {
        $item = $this->entityManager->getRepository(CartItem::class)->findOneBy(['id'=>$itemId]);
        $newPrice = $this->vm->getVarientPrice($item->getVarientId(),$this->entityManager->getRepository(VarientRepository::class));
        if($newPrice != $item->getPrice()) { #check: !=
            if($update){
                $item->setPrice($newPrice);
            }
            return false;
        }
        return true;
    }
}