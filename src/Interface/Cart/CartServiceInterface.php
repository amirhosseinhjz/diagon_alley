<?php

namespace App\Interface\Cart;

use App\Entity\Cart\Cart;

#ToDo add byId versions and by object versions
Interface CartServiceInterface
{
    public function getTotalPrice(Cart $cart);

    public function getTotalPriceById($cartId);

    public function removeCart($cart);

    public function updateStatus($cartId, $status); #tc: return type

    public function addItemToCart(array $item); #tc

    public function removeItemFromCart(array $item); #tc

    public function getCartById(int $cartId);

    public function checkItems(Cart $cart, bool $update); #todo: check every item's price and availability, return true if it stays the same
    #todo: what to do when the price has changed, or the item does not exist

}