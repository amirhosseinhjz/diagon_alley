<?php

namespace App\Interface\Cart;

use App\DTO\Cart\CartItemDTO;
use App\Entity\Cart\Cart;
use App\Entity\Cart\CartItem;
use App\Entity\Discount;
use App\Entity\Variant\Variant;
use Symfony\Component\HttpFoundation\Request;


#ToDo: read order class and apply changes accordingly
interface CartItemServiceInterface
{
    function confirmStocksById(int $itemId, bool $update):bool; #is the item available in the requested count? new count?

    function getCartItemByVariant(Cart $cart, Variant $variant):CartItem;

    function getCartItemById(int $id):CartItem;

    function createDTOFromCartItem(CartItem $item): cartItemDTO;

    function removeCartItemById(int $id);

    function createCartItemFromArray(array $array):CartItem;

    function updateCartItemById(int $id, int $quantity):CartItem;

    public function getRequestBody(Request $request);

}