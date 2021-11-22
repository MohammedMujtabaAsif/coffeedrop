<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait LocationTrait
{
    // Make API request to postcodes.io to get location data about the postcode
    public function getLocationData($postcode)
    {
        $response = Http::get('api.postcodes.io/postcodes/' . $postcode);
        if ($response->ok()) {
            return $response->json();
        }
        return false;
    }

    // Pull the Longitude and Latitude data from the json response from postcodes.io
    public function getLongLat($postcode)
    {
        $data = $this->getLocationData($postcode);
        if ($data != false) {
            $longlat = [$data['result']['longitude'], $data['result']['latitude']];
            return $longlat;
        }
        return false;
    }
}
