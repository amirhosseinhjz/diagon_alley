<?php

namespace App\Service\Discount;

use App\Entity\Discount\Discount;
use App\Entity\Order\Purchase;
use App\Interface\Order\OrderManagementInterface;
use App\Entity\User\Customer;
use App\DTO\Discount\DiscountDTO;
use App\Interface\Discount\DiscountServiceInterface;
use App\Repository\Discount\DiscountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function PHPUnit\Framework\isEmpty;

class DiscountService implements DiscountServiceInterface
{
    private EntityManagerInterface $entityManager;
    private Serializer $serializer;
    private ValidatorInterface $validator;
    private DiscountRepository $repository;
    private OrderManagementInterface $orderManagement;


    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator,
    OrderManagementInterface $orderManagement)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $this->repository = $this->entityManager->getRepository(Discount::class);
        $this->orderManagement = $orderManagement;
    }

    #ToDo: check
    public function getRequestBody(Request $request): array
    {
        return json_decode($request->getContent(), true);
    }

    public function getRequestDTO(Request $request):DiscountDTO
    {
        try {
            return $this->serializer->denormalize($this->getRequestBody($request), DiscountDTO::class);
        }catch (NotNormalizableValueException $exception){
            throw new \Exception("Invalid request structure.");
        }
    }


    public function createDiscountFromDTO(DiscountDTO $dto, bool $flush = true): Discount
    {
        $discount = new Discount();
        $this->updatePropertiesFromDTO($dto, $discount, $flush);
        return $discount;
    }

    public function createValidDTOFromArray(array $array):DiscountDTO
    {
        $dto = $this->arrayToDTO($array);
        $DTOErrors = $this->validateDiscountDTO($dto);
        if (count($DTOErrors) > 0) {
            throw (new \Exception(json_encode($DTOErrors)));
        }
        return $dto;
    }

    public function createDiscountFromArray(array $array): Discount
    {
        $dto = $this->createValidDTOFromArray($array);
        $discount = $this->createDiscountFromDTO($dto);

        return $discount;
    }

    public function createDTOFromDiscount(Discount $discount): DiscountDTO
    {
        $dto= new DiscountDTO();
        foreach ($discount as $key => $value) {
            $getterName = 'get' . ucfirst($key);
            $setterName = 'set' . ucfirst($key);
            $dto->$setterName($discount->$getterName());
        }
        return $dto;
    }

    public function updateDiscountFromDTO(DiscountDTO $dto, bool $flush = true): Discount #ToDo: use code?
    {
        if(empty($dto->getId()))
            throw new \Exception("No Discount Id was provided");
        $discount = $this->getDiscountById($dto->getId());
        if(empty($discount))
            throw new \Exception("Discount does not exist");
        $this->updatePropertiesFromDTO($dto, $discount, $flush);
        return $discount;
    }

    public function updateDiscountFromArray(array $array): Discount
    {
        $dto = $this->createValidDTOFromArray($array);
        $discount = $this->updateDiscountFromDTO($dto);
        return $discount;
    }

    public function arrayToDTO(array $array)
    {
        return $this->serializer->deserialize(json_encode($array), DiscountDTO::class, 'json');
    }


    private function validateDiscountDTO($object): array
    {
        $errors = $this->validator->validate($object);
        $errorsArray = [];
        foreach ($errors as $error) {
            $errorsArray[] = $error->getMessage();
        }
        return $errorsArray;
    }

    public function getDiscountsByCode(string $code):array
    {
        $discounts = $this->repository->findBy(['code'=>$code]);
        return $discounts;
    }

    public function getActiveDiscountByCode(string $code):Discount
    {
        $discount = $this->repository->findOneBy(['code' => $code, 'isActive' => true]);
        return $discount;
    }

    public function getDiscountById(string $id):Discount
    {
        return $this->repository->findOneBy(['id' => $id]);
    }

    public function removeDiscountByID(int $id) #ToDo: what if the discount is already applied to a few paid and unpaid orders
    {
        $discount = $this->getDiscountById($id);
        if(empty($discount)) {
            throw new \Exception("Discount does not exist.");
        }
        $this->entityManager->remove($discount);
        $this->entityManager->flush();
    }

    public function applyDiscountToPurchase(Discount $discount, Purchase $purchase, bool $flush = true): Purchase
    {
        if(!$this->checkApplicability($discount,$purchase)){
            throw new \Exception("The discount is not applicable to this purchase");
        }
        $discountValue = $this->getDiscountValue($purchase,$discount);
        $discount->addAffectedOrder($purchase);
        $purchase->setTotalPrice($purchase->getTotalPrice() - $discountValue);
        $this->entityManager->flush();
        return $purchase;
    }

    public function removeDiscountFromPurchase(Purchase $purchase): Purchase
    {
        if(empty($purchase->getDiscount())){
            throw new \Exception("Discount not found");
        }
        $price = $this->orderManagement->rawTotalPrice($purchase);  //check: consider item discounts
        $purchase->getDiscount()->removeAffectedOrder($purchase);
        $purchase->setTotalPrice($price);
        $this->entityManager->flush();
        return $purchase;
    }


    public function checkApplicability(Discount $discount, Purchase $purchase): bool
    {
        if(!$discount->isActive()){
            return false;
            //throw new \Exception("Discount is disabled.");
        }
        if($this->timesUsed($discount)>= $discount->getMaxUsageTimes() ||
            $this->timesUsed($discount, $purchase->getCustomer())>=$discount->getMaxUsageTimesPerUser()){
            return false;
            //throw new \Exception("Discount is used up.");
        }
        $time = new \DateTime('now');
        if(!empty($discount->getActivationTime()) && $time<$discount->getActivationTime()) { #should I do it softly? e.g. just minutes.
            return false;
            //throw new \Exception("Discount is unavailable at this time");
        }
        if(!empty($discount->getExpirationTime()) && $time>$discount->getExpirationTime()) { #should I do it softly? e.g. just minutes.
            return false;
            //throw new \Exception("Discount is unavailable at this time");
        }
        if(!empty($purchase->getDiscount())) {
            $this->removeDiscountFromPurchase($purchase);  //check: should I throw an exception instead?
        }
        if(!empty($discount->getMinPurchaseValue()) && $purchase->getTotalPrice()<$discount->getMinPurchaseValue()){
            return false;
            //throw new \Exception("Total order price is below the minimum");
        }
        return true;
    }

    public function getDiscountValue(Purchase $purchase, Discount $discount):float{
        return min($purchase->getTotalPrice() * $discount->getPercent(), $discount->getMaxDiscountValue());
    }

    public function toggleActivity(Discount $discount): Discount
    {
        $discount->setActivity(!$discount->isActive());
        return $discount;
    }

    public function timesUsed(Discount $discount, Customer $customer = null): int
    {
        return $this->repository->count(["discount" => $discount, "customer" => $customer]);
    }

    private function updatePropertiesFromDTO(DiscountDTO $dto, Discount $discount, bool $flush): void
    {
        foreach ($dto as $key => $value) {
            #ToDo: remove next line
            if ($key === "id")
                continue;
            $getterName = 'get' . ucfirst($key);
            $setterName = 'set' . ucfirst($key);
            $discount->$setterName($dto->$getterName());
        }
        $this->entityManager->persist($discount);
        if ($flush) {
            $this->entityManager->flush();
        }
    }
}