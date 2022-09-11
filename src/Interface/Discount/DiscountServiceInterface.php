<?php

namespace App\Interface\Discount;

use App\Entity\Discount;
use App\Entity\Order\Purchase;
use App\Entity\Order\PurchaseItem;

interface DiscountServiceInterface
{
    public function createDiscountFromDTO(DiscountDTO $dto);

    public function createDTOFromDiscount(Discount $discount);

    public function applyDiscountToPurchase(Discount $discount, Purchase $purchase);

    public function isApplicableToPurchaseItem(Discount $discount, PurchaseItem $purchaseItem);

    public function ToggleActivity(Discount $discount);
    #ToDo: schedule expiration

    public function updateDiscountFromDTO(Discount $discount, DTO $dto);
    #ToDo: if purchase is cancelled, the discounts must be marked unused, if it is completed, the discounts must be marked used
}