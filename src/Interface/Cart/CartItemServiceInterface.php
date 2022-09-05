<?php

namespace App\Interface\Cart;

interface CartItemServiceInterface
{
    function checkStocks(); #is the item available in the requested count? new count?

    function checkPrice(); #is the item available in the requested price? new price?
}