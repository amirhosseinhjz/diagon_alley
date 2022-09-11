<?php

namespace App\Entity\Cart;

use App\Entity\Variant\Variant;
use App\Repository\Cart\CartItemRepository;
use App\Entity\Discount;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: CartItemRepository::class)]
class CartItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cart $cart = null;


    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Variant $variant = null;



    public function getId(): ?int
    {
        return $this->id;
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

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): self
    {
        $this->cart = $cart;

        return $this;
    }

    public function increaseQuantity(int $n = 1){
        $this->quantity += $n;
    }

    public function decreaseQuantity(int $n = 1){
        if($n>$this->quantity)
            $this->quantity = 0;
        else
            $this->quantity = $this->quantity - $n;
    }

    public function getPrice(): ?int
    {
        return $this->variant->getPrice()*$this->quantity; #check, do i need to check the stocks too?
    }

    public function getVariant(): ?Variant
    {
        return $this->variant;
    }

    public function setVariant(?Variant $variant): self
    {
        $this->variant = $variant;

        return $this;
    }
}
