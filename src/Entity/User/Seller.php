<?php

namespace App\Entity\User;

use App\Repository\UserRepository\SellerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SellerRepository::class)]
#[UniqueEntity(fields: ["shopSlug"], message: "This shopSlug is already in use")]
class Seller extends User
{
    const SELLER = 'ROLE_SELLER';

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['elastica'])]
    private ?string $shopSlug = null;

    public function getRoles(): array
    {
        return [self::SELLER];
    }
    public function getShopSlug(): ?string
    {
        return $this->shopSlug;
    }

    public function setShopSlug(string $shopSlug): self
    {
        $this->shopSlug = $shopSlug;

        return $this;
    }


    public function eraseCredentials()
    {

    }

    public function getUserIdentifier() : string
    {
        return $this->getPhoneNumber();
    }
}
