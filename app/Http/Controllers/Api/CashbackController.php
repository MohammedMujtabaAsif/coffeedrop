<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cashback;
use App\Models\Userrequest;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class CashbackController extends Controller
{
    use ResponseTrait;

    public function __construct()
    {
        $this->middleware('auth:api')->only('history');
    }

    /**
     *  Accepts a quantity of each of the three sizes of used coffee pods as raw post data in the format
     *     {
     *       "Ristretto":10,
     *       "Espresso":100,
     *       "Lungo":30,
     *    }
     *
     *  and returns the amount in pounds and pence that the client will receive in cashback according to the following rules:
     *
     *  The first 50 capsules: [Ristretto = 2p, Espresso = 4p, Lungo = 6p]
     *  Capsules 50-500: [Ristetto = 3p, Espresso = 6p Lungo = 9p]
     *  Capsules 501+: [Ristretto = 5p, Espresso = 10p, Lungo = 15p]
     */
    public function calculate(Request $request)
    {
        $rule = 'integer|gte:0';
        $request->validate([
            'Ristretto' => $rule,
            'Espresso' => $rule,
            'Lungo' => $rule,
        ]);

        // Collect coffee values from $request
        $ris = $request->Ristretto;
        $esp = $request->Espresso;
        $lun = $request->Lungo;

        $userReq = Userrequest::create(['ip' => $request->ip()]);

        // Calculate cashback for each coffee pod type
        $risCash = $ris === 0 ? 0 : $this->calcRistretto($ris, $userReq->id);
        $espCash = $esp === 0 ? 0 : $this->calcEspresso($esp, $userReq->id);
        $lunCash = $lun === 0 ? 0 : $this->calcLungo($lun, $userReq->id);

        // Add up each cahback amount
        $total = $risCash + $espCash + $lunCash;

        return $this->responseWithData([
            'Ristretto' => $risCash,
            'Espresso' => $espCash,
            'Lungo' => $lunCash,
            'Total' => $total,
        ]);
    }

    /**
     *  Return the 5 most request cashback requests and data about
     *  the user who requested them
     */
    public function history()
    {
        // Collect the unique request ids as an array
        $userReqs = Cashback::select('userrequest_id')
            ->distinct()
            ->get()
            ->toArray();

        // Count how many unique values are available to be used in the loop codition
        $count = count($userReqs);

        // Secondary condition on the loop
        $max = 5;
        $idx = 0;

        // Array to store the most recent request's ids
        $recentReqs = [];

        // Continue to loop through the ids until:
        // the limit is reached or there are no more to search
        while ($idx < 5 && $count > 0) {
            $recentReq = $userReqs[$count - 1];
            $recentReqs[] = $recentReq;
            $count--;
            $idx++;
        }

        // Check there is recent history
        if (empty($recentReqs)) {
            // Return a response message if there is no recent history
            return $this->responseWithMessageFailed('No recent history');
        }

        // Retrieve the cashback requests that were set in the loop above
        // Load the userrequest information for each request
        $data = Cashback::whereIn('userrequest_id', $recentReqs)
            ->with('userrequest')
            ->get()
            ->groupBy('userrequest_id');

        // Return the cashback requests that have been collected
        return $this->responseWithData($data);
    }

    private function create($coffee, $userReqId, $count, $cashback)
    {
        return Cashback::create([
            'coffee' => $coffee,
            'userrequest_id' => $userReqId,
            'count' => $count,
            'cashback' => $cashback,
        ]);
    }

    /**
     * Calculate the value of the Ristretto coffee pods in pounds
     * @param count of Ristretto coffee pods
     * @return total pounds
     */
    private function calcRistretto($count, $userReqId)
    {
        // Split the coffee amounts into their respective groups
        $counts = $this->splitCoffeeAmount($count);

        // Calculate the value of each group
        $oneTo49 = $counts[0] * 2;
        $fiftyTo500 = $counts[1] * 3;
        $over500 = $counts[2] * 5;

        // Total up the value of the groups
        $total = $oneTo49 + $fiftyTo500 + $over500;
        $cb = $this->create('ristretto', $userReqId, $count, $total / 100);

        return $cb->cashback;
    }

    /**
     * Calculate the value of the Espresso coffee pods in pounds
     * @param count of Espresso coffee pods
     * @return total pounds
     */
    private function calcEspresso($count, $userReqId)
    {
        $counts = $this->splitCoffeeAmount($count);
        $oneTo49 = $counts[0] * 4;
        $fiftyTo500 = $counts[1] * 6;
        $over500 = $counts[2] * 10;

        $total = $oneTo49 + $fiftyTo500 + $over500;
        $cb = $this->create('espresso', $userReqId, $count, $total / 100);

        return $cb->cashback;

    }

    /**
     * Calculate the value of the Lungo coffee pods in pounds
     * @param count of Lungo coffee pods
     * @return total pounds
     */
    private function calcLungo($count, $userReqId)
    {
        $counts = $this->splitCoffeeAmount($count, $userReqId);

        $oneTo49 = $counts[0] * 6;
        $fiftyTo500 = $counts[1] * 9;
        $over500 = $counts[2] * 15;

        $total = $oneTo49 + $fiftyTo500 + $over500;
        $cb = $this->create('lungo', $userReqId, $count, $total / 100);

        return $cb->cashback;

    }

    /**
     * Split the requested coffee pod amounts into sections:
     *   1-49, 50-500, 500+
     * @param $count number of coffee pods to be recycled
     * @return Array number of coffee pods split into their groups
     */
    private function splitCoffeeAmount($count)
    {
        // Set initial values of each group
        $oneTo49 = 0;
        $fiftyTo500 = 0;
        $over500 = 0;

        // Spec has an overlap in the grouping.
        // First req states first 50 should be considered a group.
        // However, the second group also wants 50 in the group.
        // Decided grouping to be 1-49, 50-500, 500+

        // Switch case to calculate the values for each group
        switch ($count) {
            // If the user requested 501+ Capsules
            case $count > 500:
                $oneTo49 = 50;
                $fiftyTo500 = 450;
                $over500 = $count - 500;
                break;

            // If the user requested 50-500 Capsules
            case $count >= 50 && $count <= 500:
                $oneTo49 = 50;
                $fiftyTo500 = $count - 50;
                break;

            // If the user requested 1-49 Capsules
            case $count < 50:
                $oneTo49 = $count;
                break;
        }
        return [$oneTo49, $fiftyTo500, $over500];
    }
}
