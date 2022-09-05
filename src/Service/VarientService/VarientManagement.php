<?php

namespace App\Service\VarientService;

use App\Repository\ProductItem\VarientRepository;
use App\Entity\ProductItem\Varient;
use App\DTO\ProductItem\VarientDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class VarientManagement
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
        return $this->serializer->deserialize(json_encode($array), VarientDTO::class, 'json');
    }


    public function createVarientFromDTO(VarientDTO $dto, $flush=true) : Varient
    {
        $varient = new Varient();
        $varient->setQuantity($dto->quantity);
        $varient->setPrice($dto->price);
        $varient->setSerial("0");
        $varient->setStatus(false);
        $varient->setDescription($dto->description);
        $this->em->persist($varient);
        if ($flush) {
            $this->em->flush();
            $varient->setSerial(md5($varient->getId()));
            $this->em->flush();
        }
        return $varient;
    }

    public function readVarient($serial,VarientRepository $varientRepository){
        $varient = $varientRepository->findBy(['serial' => $serial]);
        if($varient === null)throw new \Exception("Invalid serial number");
        return $varient[0];
    }

    public function updateVarient($serial,int $quantity,int $price,VarientRepository $varientRepository){
        $varient = $this->readVarient($serial,$varientRepository);
        $varient->setQuantity($quantity)->setPrice($price);
        $this->em->flush();
        return $varient;
    }

    public function deleteVarient($serial,VarientRepository $varientRepository){
        $varient = $this->readVarient($serial,$varientRepository);
        $this->em->remove($varient);
        $this->em->flush();
        return $varient;
    }

    public function confirmVarient($serial,VarientRepository $varientRepository){
        $varient = $this->readVarient($serial,$varientRepository);
        $time = new \DateTimeImmutable('now',new \DateTimeZone('Asia/Tehran'));
        $varient->setStatus(true);
        $varient->setCreatedAt($time);
        $this->em->flush();
        return $varient;
    }

    #ToDo: change these: remove the repository
    public function getVarientPrice($serial,VarientRepository $varientRepository):int{
        $varient = $varientRepository->findBy(['serial' => $serial]);
        if($varient === null)throw new \Exception("Invalid serial number");
        return $varient[0]->getPrice();
    }

    public function getVarientStock($serial, VarientRepository $varientRepository){
        $varient = $varientRepository->findBy(['serial' => $serial]);
        if($varient === null)throw new \Exception("Invalid serial number");
        return $varient[0]->getQuantity();
    }
}