<?php

namespace App\Entity\Cart;
use App\Entity\User\Customer;
use App\Repository\Cart\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{

    public const STATUS_INIT = "INIT";
//    public const STATUS_PENDING = "PENDING";
    public const STATUS_SUCCESS = "SUCCESS";
    public const STATUS_EXPIRED = "EXPIRED";

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Serializer\Groups(['Cart.create', 'Cart.read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Serializer\Groups(['Cart.read'])]
    private ?\DateTimeInterface $finalizedAt = null;

    #[ORM\OneToMany(mappedBy: 'Cart', targetEntity: CartItem::class, orphanRemoval: true)]
//    #[Serializer\Groups(['Cart.read'])]
    private Collection $items;

    #[ORM\ManyToOne(inversedBy: 'carts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Serializer\Groups(['Cart.read'])]
    private ?Customer $customer = null;

    #[ORM\Column(length: 8)]
    #[Serializer\Groups(['Cart.read'])]
    private ?string $status = null;

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFinalizedAt(): ?\DateTimeInterface
    {
        return $this->finalizedAt;
    }

    public function setFinalizedAt(\DateTimeInterface $finalizedAt): self
    {
        $this->finalizedAt = $finalizedAt;

        return $this;
    }

    /**
     * @return Collection<int, CartItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(CartItem $item): self #problem
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setCart($this);
        }
        else {
            $item->increaseQuantity();
        }
        return $this;
    }

    public function removeItem(CartItem $item): self  #problem
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getCart() === $this) {
                $item->setCart(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        if ($status === self::STATUS_EXPIRED || $status == self::STATUS_INIT || $status === self::STATUS_SUCCESS || $status===self::STATUS_PENDING) {
            $this->status = $status;
        } else {
            throw new \Exception('Invalid value for status');
        }
        return $this;
    }

    public function getTotalPrice()
    {
        $total = 0;
        foreach ($this->getItems() as $item) {
            $total += $item->getPrice();
        }
        return $total;
    }
}





