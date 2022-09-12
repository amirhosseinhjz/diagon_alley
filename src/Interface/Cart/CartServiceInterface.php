<?php

namespace App\Interface\Cart;
use App\DTO\Cart\CartDTO;
use App\Entity\Cart\Cart;
use App\Entity\User\Customer;

#ToDo add byId versions and by object versions
Interface CartServiceInterface
{
    public function getTotalPrice(Cart $cart);

    public function getTotalPriceById($cartId);

    public function removeCart($cart);

    public function updateStatus($cartId, $status);

    public function updateCartFromDTO(CartDTO $dto):Cart;

    public function createCart(Customer $user):Cart;

    public function getCartById(int $cartId):Cart;

    public function confirmItems(Cart $cart, bool $update):bool; #todo: check every item's price and availability, return true if it stays the same
    #todo: what to do when the price has changed, or the item does not exist

}