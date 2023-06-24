<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\Report\LiveTrackingResource;
use App\Models\DeviceData;
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

    /**
     * @OA\Post(
     *     path="/api/customer/daily-route",
     *     summary="Vehicle Daily Route",
     *     tags={"daily-route"},
     *     description="Show Daily Vehicle Route.",
     *     operationId="daily-route",
     *     @OA\Parameter(
     *         name="vehicle_id",
     *         in="path",
     *         description="Vehicle Id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="path",
     *         description="Report Date",
     *         required=true,
     *         @OA\Schema(
     *             type="date"
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid user supplied"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function dailyRoute(Request $request): Response
    {
        $validator = validator(
            $request->all(),
            [
                'vehicle_id'    => 'required|exists:vehicles,id',
                'date'          => 'required|date_format:Y-m-d'
            ],
            [
                'vehicle_id.required'   => 'Vehicle is Required',
                'vehicle_id.exists'     => 'Provide Valid Vehicle Info',
                'date.required'         => 'Report Date is Required',
                'date.date_format'      => 'Provide Valid Date Format:YYYY-MM-DD'
            ]
        );

        $vehicles = Vehicle::where('customer_id', Auth::user()->id)->pluck('id')->toArray();
        $validator->after(function ($validator) use ($vehicles, $request) {
            if (!in_array($request->vehicle_id, $vehicles)) :
                $validator->errors()->add('vehicle_id', 'Provide Valid Vehicle Info');
            endif;
        });

        if ($validator->fails()) :
            return Response([
                'status' => false,
                'message' => $validator->getMessageBag()->first(),
                'errors' => $validator->getMessageBag()
            ], Response::HTTP_BAD_REQUEST);
        else :
            $data = DeviceData::select('latitude', 'longitude', 'engine_status', 'rotation')->where('vehicle_id', $request->vehicle_id)->whereDate('created_at', $request->date)->get();

            return Response([
                'status'    => true,
                'message'   => 'Vehicle Daily Report',
                'data'      => $data
            ], Response::HTTP_OK);
        endif;
    }
}
