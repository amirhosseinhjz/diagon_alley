<?php

namespace App\Service\VariantService;

use App\CacheRepository\VariantRepository\CacheVariantRepository;
use App\DTO\ProductItem\VariantDTO;
use App\Entity\Variant\Variant;
use App\Repository\VariantRepository\VariantRepository;
use App\Interface\Variant\VariantManagementInterface;
use App\Entity\User\Seller;
use App\Service\Product\ProductManager;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class VariantManagement implements VariantManagementInterface
{
    private $em;
    private $serializer;
    private $variantRepository;
    private $productManager;
    private $cacheVariantRepository;

    public function __construct(EntityManagerInterface $em , VariantRepository $variantRepository , ProductManager $productManager, CacheVariantRepository $cacheVariantRepository)
    {
        $this->em = $em;
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $this->variantRepository = $variantRepository;
        $this->productManager = $productManager;
        $this->cacheVariantRepository = $cacheVariantRepository;
    }

    public function arrayToDTO(array $array)
    {
        return $this->serializer->deserialize(json_encode($array), VariantDTO::class, 'json');
    }

    public function createVariantFromDTO(VariantDTO $dto,Seller $seller, $flush=true) : Variant
    {
        $variant = new Variant();
        $variant->setQuantity($dto->quantity);
        $variant->setPrice($dto->price);
        $variant->setSerial("0");
        $variant->setValid(false);
        $variant->setDescription($dto->description);
        $variant->setSoldNumber(0);
        $variant->setSeller($seller);
        $variant->setType($dto->type);
        $variant->setDeliveryEstimate($dto->deliveryEstimate);
        $variant->setProduct($this->productManager->findOneById($dto->productId));
        $this->em->persist($variant);
        if ($flush) {
            $variant->setSerial(md5($variant->getId()));
            $this->em->flush();
        }
        return $variant;
    }

    public function readVariant($serial){
        $variant = $this->cacheVariantRepository->findOneBy(['serial' => $serial]);
        if(!$variant)throw new \Exception("Invalid serial number");
        return $variant;
    }

    public function updateVariant($serial, int $quantity, int $price){
        if($price < 1 || $quantity < 0)throw new \Exception('Invalid data');
        $variant = $this->readVariant($serial);
        $variant->setQuantity($quantity)->setPrice($price);
        $this->em->flush();
        return $variant;
    }

    public function deleteVariant($serial){
        $variant = $this->readVariant($serial);
        $this->em->remove($variant);
        $this->em->flush();
        return $variant;
    }

    public function confirmVariant($serial){
        $variant = $this->readVariant($serial);
        $time = new \DateTimeImmutable('now',new \DateTimeZone('Asia/Tehran'));
        $variant->setValid(true);
        $variant->setCreatedAt($time);
        $this->em->flush();
        return $variant;
    }

    public function showVariant($filters_eq, $filters_gt)
    {
        $criteria = Criteria::create();
        foreach($filters_eq as $filter => $value){
            $criteria->andWhere(Criteria::expr()->eq($filter, $value));
        }
        foreach($filters_gt as $filter => $value) {
            $criteria->andWhere(Criteria::expr()->gt($filter, $value));
        }
        return $this->cacheVariantRepository->showVariant($criteria);
    }

    public function getById(int $id)
    {
        return $this->cacheVariantRepository->find($id);
    }
}