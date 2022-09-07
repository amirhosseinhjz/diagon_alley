<?php

namespace App\Interface\Discount;

use App\Entity\Discount;
use App\Entity\Order\Purchase;
use App\Entity\Order\PurchaseItem;

interface DiscountServiceInterface
{
    #ToDo: add this class to config file
    #ToDo: serialiazer and groups
    public function createDiscountFromDTO(DiscountDTO $dto):Discount;

    public function createDTOFromDiscount(Discount $discount):DiscountDTO;

    public function updateDiscountFromDTO(Discount $discount, DTO $dto):Discount;

    public function removeDiscountByID(int $id);

    public function applyDiscountToPurchase(Discount $discount, Purchase $purchase):Purchase;

    public function checkApplicability(Discount $discount, Purchase $purchase):bool;

    //public function isApplicableToPurchaseItem(Discount $discount, PurchaseItem $purchaseItem, array $filters);

    public function ToggleActivity(Discount $discount);
    #ToDo: schedule expiration

    #ToDo: if purchase is cancelled, the discounts must be marked unused, if it is completed, the discounts must be marked used
}