<?php

namespace App\Interface\Cart;

use App\DTO\Cart\CartItemDTO;
use App\Entity\Cart\CartItem;

interface CartItemServiceInterface
{
    function checkStocks(int $itemId, bool $update); #is the item available in the requested count? new count?

    function checkPrice(int $itemId, bool $update); #is the item available in the requested price? new price?

    function createDTOFromCartItem(CartItem $item): cartItemDTO;

}