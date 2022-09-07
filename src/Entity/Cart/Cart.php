<?php

namespace App\Entity\Cart;
use App\Entity\Cart\CartItem;
use App\Entity\User\Customer;
use App\Entity\Payment\Payment;
use Exception;
use App\Repository\Cart\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#ToDo: generate documents
#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #ToDO: change the strings used in other files to these constants
    public const STATUS_INIT = "INIT";
    public const STATUS_PENDING = "PENDING";
    public const STATUS_SUCCESS = "SUCCESS";
    public const STATUS_EXPIRED = "EXPIRED";

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $finalizedAt = null;

    #[ORM\OneToMany(mappedBy: 'Cart', targetEntity: CartItem::class, orphanRemoval: true)]
    private Collection $items;

    #ToDo: ask Neda to merge this part with her entity
    #[ORM\ManyToOne(inversedBy: 'carts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customer $customer = null;

    #[ORM\Column(length: 8)]
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
        $this->payments = new ArrayCollection();
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
        if ($status === "INIT" || $status == "EXPIRED" || $status === "SUCCESS") {
            $this->status = $status;
        } elseif ($status === "PENDING") {
            $this->status = $status;
            #ToDo: automatic expiration (not here)
        } else {
            throw new \Exception('Invalid value for status');
        }
        return $this;
    }

}





