<?php

namespace App\Entity\Cart;

use App\Entity\Payment\Payment;
use Exception;
use App\Repository\CartRepository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $User_Id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $finalizedAt = null;

    #[ORM\OneToMany(mappedBy: 'Cart', targetEntity: CartItem::class, orphanRemoval: true)]
    private Collection $items;

    #[ORM\Column(length: 8)]
    private ?string $status = null;

    #[ORM\OneToMany(mappedBy: 'cart', targetEntity: Payment::class)]
    private Collection $payments;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->payments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->User_Id;
    }

    public function setUserId(int $User_Id): self
    {
        $this->User_Id = $User_Id;

        return $this;
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
        } else { #todo: check the stocks in the manager and increase the count
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
            throw new Exception('Invalid value for status');
        }

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setCart($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getCart() === $this) {
                $payment->setCart(null);
            }
        }

        return $this;
    }
}
