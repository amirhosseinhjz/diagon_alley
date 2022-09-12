<?php

namespace App\Interface\Product;

use App\Entity\Product\Product;
use Symfony\Component\HttpFoundation\Request;

interface ProductManagerInterface
{
    public function getRequestBody(Request $req);

    public function normalizeArray(array $array);

    public function createEntityFromArray(array $validatedArray);

    public function deleteById(int $id);

    public function updateEntity(Product $product, array $updates);

    public function addFeature(int $id, array $features);

    public function getValidFeatureValuesByProduct(Product $product);

    public function removeFeature(int $id, array $features);

    public function findById(int $id);

    public function toggleActivity(int $id, bool $active);

    public function findBrandProducts(int $id, array $options);

    public function findCategoryProducts(int $id, array $options);
}