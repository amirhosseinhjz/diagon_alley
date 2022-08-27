<?php

namespace App\DTO\ProductItem;

use Symfony\Component\Validator\Constraints as Assert;

class VarientDTO{
    #[Assert\GreaterThan(1)]
    #[Assert\Type(type:'int')]
    public ?int $price = null;

    #[Assert\PositiveOrZero()]
    #[Assert\Type(type:'int')]
    public ?int $quantity = null;

    public ?string $description = null;

    public function setSerial(string $serial): self{
        $this->serial = $serial;
        return $this;
    }

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