<?php

namespace App\Interface\Variant;

use App\DTO\ProductItem\VariantDTO;
use App\Entity\User\Seller;
use App\Entity\Variant\Variant;

interface VariantManagementInterface
{
    public function arrayToDTO(array $array);

    public function createVariantFromDTO(VariantDTO $dto,Seller $seller, $flush=true) : Variant;

    public function readVariant($serial);

    public function updateVariant($serial, int $quantity, int $price);

    public function deleteVariant($serial);

    public function confirmVariant($serial);

    public function findVariantsByProduct($productId);

    public function findInValidVariantsBySeller($sellerId);

    public function findVariantsByValidation($valid);
}