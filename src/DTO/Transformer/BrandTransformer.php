<?php

namespace App\DTO\Transformer;

use App\DTO\BrandDTO;
use App\Entity\Brand;
use App\Repository\BrandRepository;

class BrandTransformer
{
    static function transformFromEntity(Brand $brand): BrandDTO
    {
        $dto = new BrandDTO();
        $dto->name = $brand->getName();
        $dto->description = $brand->getDescription();

        return $dto;
    }
}