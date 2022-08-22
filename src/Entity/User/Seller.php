<?php

namespace App\Entity\User;

use App\Repository\UserRepository\SellerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SellerRepository::class)]
class Seller extends User
{
    #[ORM\Column(length: 255)]
    private ?string $shopSlug = null;

    public function getShopSlug(): ?string
    {
        return $this->shopSlug;
    }

    public function setShopSlug(string $shopSlug): self
    {
        $this->shopSlug = $shopSlug;

        return $this;
    }
}
