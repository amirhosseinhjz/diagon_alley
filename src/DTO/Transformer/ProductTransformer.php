<?php

namespace App\DTO\Transformer;

use App\DTO\ProductDTO;
use App\Entity\Product;

class ProductTransformer
{
    public function transformFromEntity(Product $product): ProductDTO
    {
        $dto = new ProductDTO();
        $dto->name = $product->getName();
        $dto->type = $product->getType();
        $dto->description = $product->getDescription();

        return $dto;
    }
}
