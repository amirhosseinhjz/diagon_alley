<?php

namespace App\Interface\Cart;

use App\DTO\Cart\CartItemDTO;
use App\Entity\Cart\Cart;
use App\Entity\Cart\CartItem;
use App\Entity\Variant\Variant;

#ToDo: read order class and apply changes accordingly
interface CartItemServiceInterface
{
    function checkStocks(int $itemId, bool $update):bool; #is the item available in the requested count? new count?

    function getCartItemByVariant(Cart $cart, Variant $variant):CartItem;

    function getCartItemById(int $id):CartItem;

    function createDTOFromCartItem(CartItem $item): cartItemDTO;

}