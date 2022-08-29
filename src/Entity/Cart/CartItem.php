<?php

namespace App\Entity;

use App\Repository\CartItemRepository;
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
    private ?int $Cart_Id = null;

    #[ORM\Column]
    private ?int $varientId = null;

    /*#[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $expiry_date = null;*/

    #[ORM\Column]
    private ?int $count = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cart $Cart = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    #todo: remove cartid or card
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
        return $this->varientId;
    }

    public function setVarientId(int $varientId): self
    {
        $this->varientId = $varientId;

        return $this;
    }

    /*public function getExpiryDate(): ?\DateTimeInterface
    {
        return $this->expiry_date;
    }

    public function setExpiryDate(?\DateTimeInterface $expiry_date): self
    {
        $this->expiry_date = $expiry_date;

        return $this;
    }*/

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): self
    {
        $this->count = $count;

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
    public function increaseCount(int $n = 1){
        $this->count += $n;
    }

    public function decreaseCount(int $n = 1){
        if($n>$this->count)
            $this->count = 0;
        else
            $this->count = $this->count - $n;
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
