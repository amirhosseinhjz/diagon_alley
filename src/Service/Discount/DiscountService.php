<?php

namespace App\Service\Discount;

use App\Entity\Discount\Discount;
use App\Entity\Order\Purchase;
use App\Interface\Order\OrderManagementInterface;
use App\Entity\User\Customer;
use App\DTO\Discount\DiscountDTO;
use App\Interface\Discount\DiscountServiceInterface;
use App\Repository\Discount\DiscountRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;



class DiscountService implements DiscountServiceInterface
{
    public const CODE_LENGTH = 10;

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

    public function getRequestBody(Request $request): array
    {
        return json_decode($request->getContent(), true);
    }

    /**
     * @throws ExceptionInterface
     * @throws Exception
     */
    public function getRequestDTO(Request $request):DiscountDTO
    {
        try {
            return $this->serializer->denormalize($this->getRequestBody($request), DiscountDTO::class);
        }catch (NotNormalizableValueException){
            throw new Exception("Invalid request structure.");
        }
    }


    public function createDiscountFromDTO(DiscountDTO $dto, bool $flush = true): Discount
    {
        $discount = new Discount();
        $this->updatePropertiesFromDTO($dto, $discount);
        $this->entityManager->persist($discount);
        $this->entityManager->flush();
        return $discount;
    }

    /**
     * @throws Exception
     */
    public function createValidDTOFromArray(array $array):DiscountDTO
    {
        $dto = $this->arrayToDTO($array);
        $DTOErrors = $this->validateDiscountDTO($dto);
        if (count($DTOErrors) > 0) {
            throw (new Exception(json_encode($DTOErrors)));
        }
        return $dto;
    }

    /**
     * @throws Exception
     */
    public function createDiscountFromArray(array $array): Discount
    {
        $dto = $this->createValidDTOFromArray($array);
        return $this->createDiscountFromDTO($dto);
    }

    public function createDTOFromDiscount(Discount $discount): DiscountDTO
    {
        $dto= new DiscountDTO();
        foreach ($dto as $key => $value) {
            $getterName = 'get' . ucfirst($key);
            $setterName = 'set' . ucfirst($key);
            $dto->$setterName($discount->$getterName());
        }
        return $dto;
    }

    /**
     * @throws Exception
     */
    public function updateDiscountFromDTO(Discount $discount, DiscountDTO $dto, bool $flush = true): Discount
    {
        if(empty($discount))
            throw new Exception("Discount does not exist");
        $this->updatePropertiesFromDTO($dto, $discount);
        if ($flush){
            $this->entityManager->flush();
        }
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
        return $this->repository->findBy(['code'=>$code]);
    }

    public function getActiveDiscountByCode(string $code):Discount
    {
        return $this->repository->findOneBy(['code' => $code, 'isActive' => true]);
    }

    public function getDiscountById(string $id):Discount
    {
        return $this->repository->findOneBy(['id' => $id]);
    }

    /**
     * @throws Exception
     */
    public function removeDiscountByID(int $id)
    {
        $discount = $this->getDiscountById($id);
        if(empty($discount)) {
            throw new Exception("Discount does not exist.");
        }
        if($this->timesUsed($discount)>0){
            throw new Exception("Used discounts can not be deleted.");
        }
        $this->entityManager->remove($discount);
        $this->entityManager->flush();
    }

    /**
     * @throws Exception
     */
    public function applyDiscountToPurchase(Discount $discount, Purchase $purchase, bool $flush = true): Purchase
    {
        if(!$this->checkApplicability($discount,$purchase)){
            throw new Exception("The discount is not applicable to this purchase");
        }
        $discountValue = $this->getDiscountValue($purchase,$discount);
        $discount->addAffectedOrder($purchase);
        $purchase = $this->orderManagement->applyDiscount($purchase, $discountValue, false);
        $this->entityManager->flush();
        return $purchase;
    }

    /**
     * @throws Exception
     */
    public function removeDiscountFromPurchase(Purchase $purchase): Purchase
    {
        if(empty($purchase->getDiscount())){
            throw new Exception("Discount not found");
        }
        if($purchase->getStatus() === Purchase::STATUS_PAID || $purchase->getStatus() === Purchase::STATUS_SHIPPED){
            throw new Exception("Paid purchases cannot be edited.");
        }
        $purchase->getDiscount()->removeAffectedOrder($purchase);
        $purchase = $this->orderManagement->resetPrices($purchase, false);
        $this->entityManager->flush();
        return $purchase;
    }


    /**
     * @throws Exception
     */
    public function checkApplicability(Discount $discount, Purchase $purchase): bool
    {
        if(!$discount->getIsActive()){
            return false;
        }
        if($this->timesUsed($discount)>= $discount->getMaxUsageTimes() ||
            $this->timesUsed($discount, $purchase->getCustomer())>=$discount->getMaxUsageTimesPerUser()){
            return false;
        }
        $time = new DateTime('now');
        if(!empty($discount->getActivationTime()) && $time<$discount->getActivationTime()) { #should I do it softly? e.g. just minutes.
            return false;
        }
        if(!empty($discount->getExpirationTime()) && $time>$discount->getExpirationTime()) { #should I do it softly? e.g. just minutes.
            return false;
        }
        if(!empty($purchase->getDiscount())) {
            $this->removeDiscountFromPurchase($purchase);  //check: should I throw an exception instead?
        }
        if(!empty($discount->getMinPurchaseValue()) && $purchase->getTotalPrice()<$discount->getMinPurchaseValue()){
            return false;
        }
        return true;
    }

    public function getDiscountValue(Purchase $purchase, Discount $discount):float{
        return min($purchase->getTotalPrice() * $discount->getPercent(), $discount->getMaxDiscountValue());
    }

    /**
     * @throws Exception
     */
    public function toggleActivity(Discount $discount): Discount
    {
        if(!$discount->getIsActive() && !empty($discount->getCode())){
            if($this->isCodeActive($discount->getCode())){
                throw new Exception("There is already an active discount with this code.");
            }
        }
        $discount->setIsActive(!$discount->getIsActive()); #change isActive
        return $discount;
    }

    public function timesUsed(Discount $discount, Customer $customer = null): int
    {
        return $this->repository->count(["discount" => $discount, "customer" => $customer]);
    }

    private function updatePropertiesFromDTO(DiscountDTO $dto, Discount $discount): void
    {
        foreach ($dto as $key => $value) {
            $getterName = 'get' . ucfirst($key);
            $setterName = 'set' . ucfirst($key);
            $discount->$setterName($dto->$getterName());
        }
    }

    public function isCodeActive(string $code): bool
    {
        return !empty($this->getActiveDiscountByCode($code));
    }

    public function generateUniqueCode(Discount $discount): string
    {
        return substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, self::CODE_LENGTH);
    }
}