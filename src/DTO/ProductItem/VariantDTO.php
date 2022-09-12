<?php

namespace App\DTO\ProductItem;

use Symfony\Component\Validator\Constraints as Assert;

class VariantDTO{
    #[Assert\GreaterThan(1)]
    #[Assert\Type(type:'int')]
    public ?int $price = null;

    #[Assert\PositiveOrZero()]
    #[Assert\Type(type:'int')]
    public ?int $quantity = null;

    #[Assert\Choice(['digital', 'physical'] , message: 'Choose type from \'digital\' or \'physical\' ')]
    public ?string $type = null;

    public ?string $description = null;

    #[Assert\PositiveOrZero()]
    #[Assert\Type(type:'int')]
    public ?int $productId = null;

    #[Assert\NotNull]
    #[Assert\Type(type:'int')]
    public ?int $deliveryEstimate = null;

    public function setPrice(int $price): self{
        $this->price = $price;
        return $this;
    }

    public function setQuantity(int $quantity): self{
        $this->quantity = $quantity;
        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }
}