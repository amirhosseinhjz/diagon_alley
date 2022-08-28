<?php

namespace App\DTO\UserDTOs;

use Symfony\Component\Validator\Constraints as Assert;


class SellerDTO extends UserDTO
{
    #[Assert\Length(min:3, max:255)]
    #[Assert\Type(type:'string')]
    public string $shopSlug;

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