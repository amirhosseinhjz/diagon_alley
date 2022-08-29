<?php

namespace App\Service;

use App\DTO\CartDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Cart;
use App\Entity\CartItem;


Interface CartServiceInterface
{

    public function getCart(int $userId, bool $create = true): Cart;

    public function getTotalPrice($cartId): int;

    public function removeCart($cart);

    public function updateStatus($cartId, $status); #tc: return type

    public function addItemToCart(array $item); #tc

    public function removeItemFromCart(array $item); #tc
    
}