<?php

namespace App\Interface\Feature;

use App\Entity\Variant\Variant;

interface FeatureValueManagementInterface
{
    public function defineFeatureValue($features);

    public function addFeatureValueToVariant(array $values, Variant $variant);

    public function readFeatureValueById($id);

    public function updateFeatureValue($id, $value);

    public function showFeaturesValue();

    public function deleteFeatureValue($id);
}