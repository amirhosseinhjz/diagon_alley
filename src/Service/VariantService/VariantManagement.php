<?php

namespace App\Service\VariantService;

use App\DTO\ProductItem\VariantDTO;
use App\Entity\Variant\Variant;
use App\Repository\VariantRepository\VariantRepository;
use App\Interface\Variant\VariantManagementInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class VariantManagement implements VariantManagementInterface
{
    private $em;
    private $serializer;
    private $varientRepository;

    public function __construct(EntityManagerInterface $em , VariantRepository $variantRepository )
    {
        $this->em = $em;
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $this->varientRepository = $variantRepository;
    }

    public function arrayToDTO(array $array)
    {
        return $this->serializer->deserialize(json_encode($array), VariantDTO::class, 'json');
    }

    public function createVariantFromDTO(VariantDTO $dto, $flush=true) : Variant
    {
        $variant = new Variant();
        $variant->setQuantity($dto->quantity);
        $variant->setPrice($dto->price);
        $variant->setSerial("0");
        $variant->setStatus(false);
        $variant->setDescription($dto->description);
        $variant->setSoldNumber(0);
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
        $variant->setStatus(true);
        $variant->setCreatedAt($time);
        $this->em->flush();
        return $variant;
    }
}