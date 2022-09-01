<?php

namespace App\Service\VariantService;

use App\DTO\ProductItem\VariantDTO;
use App\Entity\Variant\Variant;
use App\Repository\VariantRepository\VariantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class VariantManagement
{
    private $em;
    private $serializer;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
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
        $this->em->persist($variant);
        if ($flush) {
            $this->em->flush();
            $variant->setSerial(md5($variant->getId()));
            $this->em->flush();
        }
        return $variant;
    }

    public function readVariant($serial, VariantRepository $varientRepository){
        $variant = $varientRepository->findBy(['serial' => $serial]);
        if(!$variant)throw new \Exception("Invalid serial number");
        return $variant[0];
    }

    public function updateVariant($serial, int $quantity, int $price, VariantRepository $variantRepository){
        if($price < 1 || $quantity < 0)throw new \Exception('Invalid data');
        $variant = $this->readVariant($serial,$variantRepository);
        $variant->setQuantity($quantity)->setPrice($price);
        $this->em->flush();
        return $variant;
    }

    public function deleteVariant($serial, VariantRepository $variantRepository){
        $variant = $this->readVariant($serial,$variantRepository);
        $this->em->remove($variant);
        $this->em->flush();
        return $variant;
    }

    public function confirmVariant($serial, VariantRepository $variantRepository){
        $variant = $this->readVariant($serial,$variantRepository);
        $time = new \DateTimeImmutable('now',new \DateTimeZone('Asia/Tehran'));
        $variant->setStatus(true);
        $variant->setCreatedAt($time);
        $this->em->flush();
        return $variant;
    }
}