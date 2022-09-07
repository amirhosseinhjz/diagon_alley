<?php

namespace App\Entity\Cart;

use App\Repository\Cart\CartItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#ToDo: get price, etc from variant
#toDo: change varient to variant everywhere
#[ORM\Entity(repositoryClass: CartItemRepository::class)]
class CartItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $variantId = null;
    #ToDo: turn into a relation
    /*#[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $expiry_date = null;*/

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cart $Cart = null;

    #ToDo get from the varient
    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;
    #todo: remove

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVariantId(): ?int
    {
        return $this->variantId;
    }

    public function setVariantId(int $variantId): self
    {
        $this->variantId = $variantId;

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

    public function getCart(): ?Cart
    {
        return $this->Cart;
    }

    public function setCart(?Cart $Cart): self
    {
        $this->Cart = $Cart;

        return $this;
    }

    #ToDo: should not exceed the 
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

    #ToDo: change getPrice to get price from variant and apply quantity and discount to return the final price
}
