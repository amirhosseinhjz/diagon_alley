<?php

namespace App\Trait\Distance;

trait CalculateDistanceTrait
{
    public function findDistanceOfTwoPoints($latA, $lngA, $latB, $lngB)
    {
        $R = 6371; // Radius of the earth in km
        $dLat = deg2rad($latB - $latA);
        $dLon = deg2rad($lngB - $lngA);
        $a =
            sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($latA)) * cos(deg2rad($latB)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $d = $R * $c; // Distance in km
        return $d;
    }
}
