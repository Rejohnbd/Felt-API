<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\Report\LiveTrackingResource;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CustomerVehicleReportController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/customer/live-tracking",
     *     tags={"live-tracking"},
     *     summary="Returns all vehicle live tracking Info",
     *     description="",
     *     operationId="live-tracking",
     *      @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="shortList",
     *         required=false,
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *             @OA\AdditionalProperties(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         )
     *     ),
     *     security={
     *         {"Bearer token": {}}
     *     }
     * )
     */
    public function liveTracking()
    {
        $data = Vehicle::with([
            'vehicleType',
            'driverInfo.userDetails',
            'vehicleLatestData'
        ])->where('customer_id', Auth::user()->id)->get();

        return Response([
            'status'    => true,
            'message'   => 'Vehicle Life Info',
            'data'      => LiveTrackingResource::collection($data)
        ], Response::HTTP_OK);
    }
}
