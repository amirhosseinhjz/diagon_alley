<?php

namespace App\Entity\User;

use App\Entity\Cart\Cart;
use App\Repository\UserRepository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer extends User
{
    const CUSTOMER = 'ROLE_CUSTOMER';

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Cart::class, orphanRemoval: true)]
    private Collection $carts;

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
        return $this;
    }
}
