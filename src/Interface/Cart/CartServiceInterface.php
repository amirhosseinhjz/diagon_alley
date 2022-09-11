<?php

namespace App\Interface\Cart;
use App\DTO\Cart\CartDTO;
use App\Entity\Cart\Cart;
use App\Entity\User\Customer;

#ToDo add byId versions and by object versions
Interface CartServiceInterface
{
    public function createCart(Customer $user):Cart;

    public function getCartById(int $cartId):Cart;

}