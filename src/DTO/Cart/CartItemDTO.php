<?php

namespace App\DTO\Cart;

use Symfony\Component\Validator\Constraints as Assert;
#ToDo: revise
class CartItemDTO
{

    private ?int $cartId = null;

    private ?int $variantId = null;

    #[Assert\Positive]
    #[Assert\Type(type:'int')]
    private ?int $quantity = null;

    private ?string $discountCode = null;

    #todo: remove cartid
    public function getCartId(): ?int
    {
        return $this->Cart_Id;
    }

    public function setCartId(int $Cart_Id): self
    {
        $this->Cart_Id = $Cart_Id;

        return $this;
    }

    public function getVariantId(): ?int
    {
        return $this->VariantId;
    }

    public function setVariantId(int $VariantId): self
    {
        $this->VariantId = $VariantId;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getDiscountCode(): ?string
    {
        return $this->discountCode;
    }

    public function setDiscountCode(?string $discountCode): void
    {
        $this->discountCode = $discountCode;
    }

}