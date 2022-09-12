<?php

namespace App\Trait;

trait PaymentTrait
{

    public function calculatePaidAmount($price, $discount, $vatPercentage = 2)
    {
        $priceAfterDiscount = ($price * (100 - $discount)) / 100;
        $finalPrice = ($priceAfterDiscount * $vatPercentage) / 100;

        return $finalPrice;
    }
}
