<?php

namespace App\Service\VariantService;

use App\DTO\ProductItem\VariantDTO;
use App\Entity\Variant\Variant;
use App\Repository\VariantRepository\VariantRepository;
use App\Interface\Variant\VariantManagementInterface;
use App\Entity\User\Seller;
use App\Service\Product\ProductManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class VariantManagement implements VariantManagementInterface
{
    private $em;
    private $serializer;
    private $varientRepository;
    private $productManager;

    public function __construct(EntityManagerInterface $em , VariantRepository $variantRepository , ProductManager $productManager)
    {
        $this->em = $em;
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $this->varientRepository = $variantRepository;
        $this->productManager = $productManager;
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
        $variant->setProduct($this->productManager->findOneById($dto->productId));
        $this->em->persist($variant);
        if ($flush) {
            $this->em->flush();
            $variant->setSerial(md5($variant->getId()));
            $this->em->flush();
        }
        return $variant;
    }

    public function readVariant($serial){
        $variant = $this->varientRepository->findBy(['serial' => $serial]);
        if(!$variant)throw new \Exception("Invalid serial number");
        return $variant[0];
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
        return $this->varientRepository->showVariant($filters_eq,$filters_gt);
    }
}