<?php

namespace App\Interface\Discount;

use App\Entity\Discount\Discount;
use App\DTO\Discount\DiscountDTO;
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

    public function createDiscountFromArray(array $array):Discount;

    public function createDTOFromDiscount(Discount $discount):DiscountDTO;

    public function updateDiscountFromDTO(DiscountDTO $dto):Discount;

    public function updateDiscountFromArray(Array $array):Discount;

    public function getDiscountsByCode(string $code):array;

    public function getActiveDiscountByCode(string $code):Discount;

    public function getDiscountById(string $id):Discount;

    public function removeDiscountByID(int $id);

    public function applyDiscountToPurchase(Discount $discount, Purchase $purchase):Purchase;

    public function removeDiscountFromPurchase(Purchase $purchase):Purchase;

    public function checkApplicability(Discount $discount, Purchase $purchase):bool;

    public function toggleActivity(Discount $discount):Discount;

    public function timesUsed(Discount $discount, Customer $customer = null):int;

    #ToDo: schedule expiration

    #ToDo: if purchase is cancelled, the discounts must be marked unused, if it is completed, the discounts must be marked used

    #ToDo:
    //public function isApplicableToPurchaseItem(Discount $discount, PurchaseItem $purchaseItem, array $filters):bool;
    //public function setUniqueCode(Discount $discount):string;
}