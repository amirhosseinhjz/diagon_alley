<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CartItembDTO
{
    

private ?int $Cart_Id = null;

private ?int $varientId = null;

#[Assert\Positive]
#[Assert\Type(type:'int')]
private ?int $count = null;

#[Assert\PositiveOrZero]
private ?int $price = null;

#[Assert\NotBlank]
private ?string $title = null;

public function getId(): ?int
{
    return $this->id;
}

#t: remove cartid
public function getCartId(): ?int
{
    return $this->Cart_Id;
}

public function setCartId(int $Cart_Id): self
{
    $this->Cart_Id = $Cart_Id;

    return $this;
}

public function getVarientId(): ?int
{
    return $this->VarientId;
}

public function setVarientId(int $VarientId): self
{
    $this->VarientId = $VarientId;

    return $this;
}

public function getCount(): ?int
{
    return $this->count;
}

public function setCount(int $count): self
{
    $this->count = $count;

    return $this;
}

public function getPrice(): ?int
{
    return $this->price;
}

public function setPrice(int $price): self
{
    $this->price = $price;

    return $this;
}

public function getTitle(): ?string
{
    return $this->title;
}

public function setTitle(string $title): self
{
    $this->title = $title;

    return $this;
}
}