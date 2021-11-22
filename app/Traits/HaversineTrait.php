<?php

namespace App\Traits;

trait HaversineTrait
{
    /**
     * Calculate the distance between postcodes using the Haversine formula
     * @return Float $distance to 2 d.p.
     */
    public function calculateDistance($long1, $lat1, $long2, $lat2)
    {
        // Radius of Earth in Kilometers
        $earthRadius = 6371;

        // Calculate the radians value of the difference between the longitudes and latitudes
        $radLong = deg2rad($long2 - $long1);
        $radLat = deg2rad($lat2 - $lat1);

        $a = (sin($radLat / 2) ** 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * (sin($radLong / 2) ** 2);
        $c = 2 * asin(sqrt($a));
        $distance = $earthRadius * $c;

        // Return the distance to 2 decimal places in Kilometers
        return round($distance, 2);
    }
}
