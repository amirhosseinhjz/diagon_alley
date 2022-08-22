<?php

namespace App\Service\UserService;

use \App\Repository\UserRepository\SellerRepository;
use \App\Entity\User\Seller;
use App\DTO\UserDTOs\SellerDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
//use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class SellerService
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
        return $this->serializer->deserialize(json_encode($array), SellerDTO::class, 'json');
    }

    public function createSellerFromDTO(SellerDTO $dto, $flush=true) : Seller
    {
        $seller = new Seller();
        $seller->setName($dto->name);
        $seller->setLastName($dto->lastName);
        $seller->setEmail($dto->email);
        $seller->setPassword($dto->password);
        $seller->setPhoneNumber($dto->phoneNumber);
        $seller->setshopSlug($dto->shopSlug);
        $seller->setRoles($dto->roles);
        $this->em->persist($seller);
        if ($flush) {
            $this->em->flush();
        }
        return $seller;
    }
}