<?php

namespace Database\Seeders;

use App\Models\Day;
use App\Models\Location;
use App\Traits\LocationTrait;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    use LocationTrait;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Empty the databases
        Location::truncate();
        Day::truncate();

        // Set the days of the week
        $days = ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"];

        // Open the file that will be read
        $dataFile = fopen(base_path('/database/data/location_data.csv'), 'r');

        // Track first line of titles in the csv file
        $isFirstLine = true;

        // Continue to loop through the csv file until it reaches the end of the file
        while (($data = fgetcsv($dataFile, 2000, ',')) !== false) {

            // Check the $data is not the titles line
            if (!$isFirstLine) {

                $longlat = $this->getLongLat($data[0]);
                $long = $longlat[0];
                $lat = $longlat[1];

                // Store a Location model containing the postcode of the CoffeeDrop location
                $location = Location::create(
                    [
                        'postcode' => $data[0],
                        'longitude' => $long,
                        'latitude' => $lat,
                    ]);

                // Tracks the number of days that have been saved
                $dayCount = 1;

                // Loop through the day creation for the number of days in a week
                while ($dayCount < 7) {

                    // Retrive the open and close time for this day of the week
                    $opentime = $data[$dayCount];
                    $closetime = $data[$dayCount + 7];

                    // Check the opening time collected is not empty.
                    if ($opentime != '') {
                        // Store a Day model
                        Day::create([
                            // Use the location id from the Location model created above
                            'location_id' => $location->id,
                            // Store the day's number in the week
                            'day' => $days[$dayCount - 1],
                            'opentime' => $opentime,
                            'closetime' => $closetime,
                        ]);
                    }

                    // Increment the counter to store the next day
                    $dayCount++;
                }
            }
            $isFirstLine = false;
        }
    }
}
