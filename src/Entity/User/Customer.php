<?php

namespace App\Entity\User;

use App\Entity\Cart\Cart;
use App\Entity\Order\Purchase;
use App\Repository\UserRepository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer extends User
{
    const CUSTOMER = 'ROLE_CUSTOMER';

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Cart::class, orphanRemoval: true)]
    private Collection $carts;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Purchase::class)]
    private Collection $purchases;

    public function __construct()
    {
        parent::__construct();
        $this->purchases = new ArrayCollection();
    }

    public function getRoles(): array
    {
        return [self::CUSTOMER];
    }


    public function eraseCredentials()
    {

    }

    public function getUserIdentifier() : string
    {
        return $this->getPhoneNumber();
    }

    public function getCarts()
    {
        return $this->carts;
    }


    public function addCart(Cart $cart)
    {
        if (!$this->carts->contains($cart)) {
            $this->carts->add($cart);
            $cart->setCustomer($this);
        }
        return $this;
    }

    public function removeCart(Cart $cart)
    {
        if ($this->carts->removeElement($cart)) {
            if ($cart->getCustomer() === $this) {
                $cart->setCustomer(null);
            }
        }
    }

    /**
     * @return Collection<int, \App\Entity\Order\Purchase>
     */
    public function getPurchases(): Collection
    {
        return $this->purchases;
    }

    public function addPurchase(Purchase $purchase): self
    {
        if (!$this->purchases->contains($purchase)) {
            $this->purchases->add($purchase);
            $purchase->setCustomer($this);
        }

        return $this;
    }

    public function removePurchase(Purchase $purchase): self
    {
        if ($this->purchases->removeElement($purchase)) {
            // set the owning side to null (unless already changed)
            if ($purchase->getCustomer() === $this) {
                $purchase->setCustomer(null);
            }
        }

        return $this;
    }
}
