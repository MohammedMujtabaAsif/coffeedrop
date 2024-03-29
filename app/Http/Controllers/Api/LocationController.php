<?php

namespace App\Http\Controllers\Api;

use App\Models\Day;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Traits\LocationTrait;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;

class LocationController extends Controller
{
    use ResponseTrait, LocationTrait;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Create a new CoffeeDrop location with a postcode and opening times
     */
    public function create(Request $request)
    {
        // Check the postcode is a string, exists, and is not stored in the locations table yet
        // Check the opening_times array contains at least 1 day and a max of 7
        // Check the closing_times array is the same size as the opening_times array
        $loc = $request->validate([
            'postcode' => 'bail|required|string|unique:locations',
            'opening_times' => 'required|array|min:1|max:7',
            'closing_times' => 'required|array|size:' . count($request->opening_times),
        ]);

        $pc = $request->postcode;

        $longlat = $this->getLongLat($pc);

        // Create a new Location model with the provided postcode
        $loc = Location::create([
            'postcode' => $pc,
            'longitude' => $longlat[0],
            'latitude' => $longlat[1],
        ]);

        // Abbreviate the times from the request
        $ots = $request->opening_times;
        $cts = $request->closing_times;

        // Since opening_times and closing_times must be the same size and should use same keys
        // Loop through opening_times and use the current key
        // Store the times for this day at the location
        foreach ($ots as $key => $ot) {
            Day::create([
                'location_id' => $loc->id,
                'day' => $key,
                'opentime' => $ot,
                'closetime' => $cts[$key],
            ]);
        }

        // Loads the days relations to be sent to the user
        $loc->days;

        return $this->responseWithData([$loc]);
    }

}
