<?php

namespace App\Interface\Discount;

use App\Entity\Discount;
use App\Entity\Order\Purchase;
use App\Entity\Order\PurchaseItem;
use App\Entity\User\Customer;
use Symfony\Component\HttpFoundation\Request;

interface DiscountServiceInterface
{
    #ToDo: add this class to config file
    #ToDo: serialiazer and groups
    public function getRequestBody(Request $request):array;

    public function createDiscountFromDTO(DiscountDTO $dto):Discount; #ToDo: remove from here

    public function createDiscountFromArray(DiscountDTO $dto):Discount;

    public function createDTOFromDiscount(Discount $discount):DiscountDTO;

    public function updateDiscountFromDTO(DTO $dto):Discount;

    public function updateDiscountFromArray(Array $array):Discount;

    public function getDiscountByCode(string $code);

    public function getDiscountById(string $id);

    public function removeDiscountByID(int $id);

    public function applyDiscountToPurchase(Discount $discount, Purchase $purchase):Purchase;

    public function removeDiscountToPurchase(Discount $discount, Purchase $purchase):Purchase;

    public function checkApplicability(Discount $discount, Purchase $purchase):bool;

    public function toggleActivity(Discount $discount):Discount;

    public function timesUsed(Discount $discount, Customer $customer = null):int;

    #ToDo: schedule expiration

    #ToDo: if purchase is cancelled, the discounts must be marked unused, if it is completed, the discounts must be marked used

    #ToDo:
    //public function isApplicableToPurchaseItem(Discount $discount, PurchaseItem $purchaseItem, array $filters):bool;
    //public function setUniqueCode(Discount $discount):string;
}