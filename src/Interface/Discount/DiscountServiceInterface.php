<?php

namespace App\Interface\Discount;

use App\Entity\Discount\Discount;
use App\DTO\Discount\DiscountDTO;
use App\Entity\Order\Purchase;
use App\Entity\User\Customer;
use Symfony\Component\HttpFoundation\Request;

interface DiscountServiceInterface
{

    public function getRequestBody(Request $request):array;

    public function createDiscountFromDTO(DiscountDTO $dto):Discount;

    public function createDiscountFromArray(array $array):Discount;

    public function createDTOFromDiscount(Discount $discount):DiscountDTO;

    public function updateDiscountFromDTO(Discount $discount, DiscountDTO $dto):Discount;

    public function getRequestDTO(Request $request):DiscountDTO;

    public function getDiscountsByCode(string $code):array;

    public function getActiveDiscountByCode(string $code):Discount;

    public function getDiscountById(string $id):Discount;

    public function removeDiscountByID(int $id);

    public function applyDiscountToPurchase(Discount $discount, Purchase $purchase):Purchase;

    public function removeDiscountFromPurchase(Purchase $purchase):Purchase;

    public function checkApplicability(Discount $discount, Purchase $purchase):bool;

    public function toggleActivity(Discount $discount):Discount;

    public function timesUsed(Discount $discount, Customer $customer = null):int;

    public function isCodeActive(string $code);

    #ToDo: if purchase is cancelled, the discounts must be marked unused, if it is completed, the discounts must be marked used

    public function generateUniqueCode(Discount $discount):string;
}