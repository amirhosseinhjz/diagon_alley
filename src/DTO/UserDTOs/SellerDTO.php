<?php

namespace App\DTO\UserDTOs;

use Symfony\Component\Validator\Constraints as Assert;


class SellerDTO extends UserDTO
{
    #[Assert\Length(min:3, max:255)]
    public string $shopSlug;

    public function getshopSlug(): ?string
    {
        return $this->shopSlug;
    }
    public function setshopSlug(string $shopSlug): self
    {
        $this->shopSlug = $shopSlug;

        return $this;
    }
}