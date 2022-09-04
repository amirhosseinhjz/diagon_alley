<?php

namespace App\Interface\Cart;

use App\DTO\CartDTO\CartDTO;
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
    
}