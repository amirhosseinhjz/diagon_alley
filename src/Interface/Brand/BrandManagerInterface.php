<?php

namespace App\Interface\Brand;

use App\Entity\Brand\Brand;
use Symfony\Component\HttpFoundation\Request;

interface BrandManagerInterface
{
    public function getRequestBody(Request $req);

    public function serialize($data, array $groups);

    public function normalizeArray(array $array);

    public function createEntityFromArray(array $validatedArray);

    public function updateEntity(Brand $brand, array $updates);

    public function removeUnused(Brand $brand);

    public function findById(int $id);
}