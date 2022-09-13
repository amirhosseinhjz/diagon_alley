<?php

namespace App\Interface\Order;

use App\Entity\Order\Purchase;

interface OrderManagementInterface
{
    public function rawTotalPrice(Purchase $purchase);

}