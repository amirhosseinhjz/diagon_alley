<?php

namespace App\Interface\Feature;

interface FeatureManagementInterface
{
    public function addLabelsToDB(array $features);

    public function readFeatureLabel($id);

    public function updateFeatureLabel($id , $body);

    public function deleteFeatureLabel($id);

    public function showFeatureLabel();
}