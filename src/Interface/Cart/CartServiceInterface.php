<?php

namespace App\Interface\Cart;

use App\DTO\Cart\CartDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Cart\Cart;
use App\Entity\Cart\CartItem;


Interface CartServiceInterface
{

    public function getCart(int $userId, bool $create = true);

    public function getTotalPrice($cartId);

    public function removeCart($cart);

    public function updateStatus($cartId, $status); #tc: return type

    public function addItemToCart(array $item); #tc

    public function removeItemFromCart(array $item); #tc

    public function getCartById(int $cartId);

    public function checkItems(int $cartId, bool $update); #todo: check every item's price and availability, return true if it stays the same
    #todo: what to do when the price has changed, or the item does not exist

    public function getCartId(int $userId);
    
}