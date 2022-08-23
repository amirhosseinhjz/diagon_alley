<?php

namespace App\DTO\Transformer;

use App\DTO\CategoryDTO;
use App\Entity\Category;

class CategoryTransformer
{
    public function transformFromEntity(Category $category): CategoryDTO
    {
        $dto = new CategoryDTO();
        $dto->name = $category->getName();
        $dto->isLeaf = $category->isIsLeaf();

        return $dto;
    }
}
