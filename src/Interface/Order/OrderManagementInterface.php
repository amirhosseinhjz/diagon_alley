<?php

namespace App\Interface\Order;

use App\Entity\Order\Purchase;

interface OrderManagementInterface
{
    public function resetPrices(Purchase $purchase, bool $flush = true):Purchase;
    public function applyDiscount(Purchase $purchase, float $value, bool $flush = true):Purchase;

}