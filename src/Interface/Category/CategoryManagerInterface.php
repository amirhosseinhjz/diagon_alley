<?php

namespace App\Interface\Category;

use App\Entity\Category\Category;
use Symfony\Component\HttpFoundation\Request;

interface CategoryManagerInterface
{
    public function getRequestBody(Request $req);

    public function serialize($data, array $groups);

    public function normalizeArray(array $array);

    public function createEntityFromArray(array $validatedArray);

    public function updateEntity(Category $category, array $updates);

    public function removeUnused(Category $category);

    public function findBrandsById(int $id);

    public function findParentsById(string $id);

    public function findById(int $id);

    public function toggleCategoryActivity(Category $category, bool $active);

    public function toggleActivity(int $id, bool $active);

    public function getFeaturesById(int $id);

    public function addFeatures(Category $category, array $features);

    public function removeFeatures(Category $category, array $features);

    public function findMainCategories();
}