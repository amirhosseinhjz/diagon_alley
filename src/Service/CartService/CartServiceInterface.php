<?php

namespace App\Service\CartService;

use App\DTO\CartDTO\CartDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Cart\Cart;
use App\Entity\Cart\CartItem;


Interface CartServiceInterface
{

    public function getCart(int $userId, bool $create = true):?cart;
    
    public function getCartById(int $cartId):?cart;
    
    public function getTotalPrice($cartId): int;

    public function removeCart($cart);

    public function updateStatus($cartId, $status); #tc: return type

    public function addItemToCart(array $item); #tc

    public function removeItemFromCart(array $item); #tc
    
}