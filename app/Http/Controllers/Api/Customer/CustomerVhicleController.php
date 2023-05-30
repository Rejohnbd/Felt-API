<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\Vehicle\VehicleDetailsResource;
use App\Http\Resources\Customer\VhicleShortListResource;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CustomerVhicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/api/customer/customer-vehicles",
     *     tags={"customer-vehicles"},
     *     summary="Returns all customer vehicle ",
     *     description="",
     *     operationId="customer-vehicles",
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
    public function index(Request $request): Response
    {
        $data = Vehicle::with([
            'driverInfo.userDetails',
            'vehicleType',
            'servicePackage',
            'deviceInfo.deviceType'
        ])->where('customer_id', Auth::user()->id)->get();

        if ($request->type == 'shortList') :
            return Response([
                'status'    => true,
                'message'   => 'Customer Vehicle',
                'data'      => VhicleShortListResource::collection($data)
            ], Response::HTTP_OK);
        else :
            return Response([
                'status'    => true,
                'message'   => 'Customer Vehicle',
                'data'      => $data
            ], Response::HTTP_OK);
        endif;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/customer/customer-vehicles/id",
     *     tags={"customer-single-vehicle"},
     *     summary="Get customer single vehicle ",
     *     description="",
     *     operationId="customer-single-vehicle",
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
    public function show(string $id): Response
    {
        $data = Vehicle::with([
            'driverInfo.userDetails',
            'vehicleType',
            'servicePackage',
            'deviceInfo.deviceType'
        ])->where('customer_id', Auth::user()->id)->findOrFail($id);

        return Response([
            'status'    => true,
            'message'   => 'Customer Vehicle Details',
            'data'      => new VehicleDetailsResource($data)
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): Response
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        //
    }

    /**
     * @OA\Post(
     *     path="/api/customer/vehicle-update",
     *     summary="Update Vehicle Purpose",
     *     tags={"vehicle-update-purpose"},
     *     description="Update Vehicle Purpose.",
     *     operationId="vehicle-update-purpose",
     *     @OA\Parameter(
     *         name="vehicle_id",
     *         in="path",
     *         description="Vehicle Id",
     *         required=true,
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="vehicle_purpose",
     *         in="path",
     *         description="Vehicle Purpose",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="fuel_capacity",
     *         in="path",
     *         description="Fuel Congestion",
     *         required=true,
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="vehicle_kpl",
     *         in="path",
     *         description="Milage",
     *         required=true,
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="driver_id",
     *         in="path",
     *         description="Driver Id",
     *         required=false,
     *         @OA\Schema(
     *             type="number"
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
    public function vehicleUpdate(Request $request): Response
    {
        $validator = validator(
            $request->all(),
            [
                'vehicle_id'            => 'required|numeric|exists:vehicles,id',
                'fuel_capacity'         => 'required|numeric',
                'vehicle_kpl'           => 'required|numeric',
                'driver_id'             => 'nullable|numeric|exists:users,id',
            ],
            [
                'vehicle_id.required'       => 'Vehicle is Requried',
                'vehicle_id.numeric'        => 'Provide Valid Vehicle',
                'vehicle_id.exists'         => 'Provide Valid Vehicle',
                'fuel_capacity.required'    => 'Fuel Congestion is Required',
                'fuel_capacity.numeric'     => 'Provide Valid Fuel Congestion',
                'vehicle_kpl.required'      => 'Milage is Required',
                'vehicle_kpl.numeric'       => 'Provide Valid Milage',
                'driver_id.numeric'         => 'Provide Valid Driver',
                'driver_id.exists'          => 'Provide Valid Driver'
            ]
        );

        if ($validator->fails()) :
            return Response([
                'status' => false,
                'message' => $validator->getMessageBag()->first(),
                'errors' => $validator->getMessageBag()
            ], Response::HTTP_BAD_REQUEST);
        else :
            $data = Vehicle::where('customer_id', Auth::user()->id)->findOrFail($request->vehicle_id);

            $data->fuel_capacity = $request->fuel_capacity;
            $data->vehicle_kpl = $request->vehicle_kpl;
            if ($request->filled('driver_id')) :
                $data->driver_id = $request->driver_id;
            endif;
            if ($request->filled('vehicle_purpose')) :
                $data->vehicle_purpose = $request->vehicle_purpose;
            endif;
            $data->save();

            return Response([
                'status'    => true,
                'message'   => 'Driver Update Successfully',
                'data'      => array(
                    'id'                => $data->id,
                    'vehicle_purpose'   => $data->vehicle_purpose,
                    'fuel_capacity'     => $data->fuel_capacity,
                    'vehicle_kpl'       => $data->vehicle_kpl,
                    'driver_id'         => $data->driver_id,
                )
            ], Response::HTTP_CREATED);
        endif;
    }

    /**
     * @OA\Get(
     *     path="/api/customer/customer-vehicles-speed-limitation/id",
     *     tags={"customer-vehicles-speed-limitation"},
     *     summary="Get customer vehicle speed info",
     *     description="",
     *     operationId="customer-vehicles-speed-limitation",
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
    public function speedLimitation(string $id): Response
    {
        $data = Vehicle::select('id', 'speed_limitation')->where('customer_id', Auth::user()->id)->findOrFail($id);

        return Response([
            'status'    => true,
            'message'   => 'Customer Vehicle Speed Info',
            'data'      => array(
                'id'                => $data->id,
                'speed_limitation'  => $data->speed_limitation,
            )
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/customer/customer-vehicles-speed-limitation/id",
     *     tags={"customer-vehicles-speed-limitation-update"},
     *     summary="Update customer vehicle speed info",
     *     description="",
     *     operationId="customer-vehicles-speed-limitation-update",
     *     @OA\Parameter(
     *         name="speed_limitation",
     *         in="path",
     *         description="Speed Limitation",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
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
    public function speedLimitationUpdate(Request $request, string $id): Response
    {
        $validator = validator(
            $request->all(),
            [
                'speed_limitation'      => 'required|numeric',
            ],
            [
                'speed_limitation.required' => 'Speed Limitation is Required',
                'speed_limitation.numeric'  => 'Provide Valid Speed Limitation'
            ]
        );
        if ($validator->fails()) :
            return Response([
                'status' => false,
                'message' => $validator->getMessageBag()->first(),
                'errors' => $validator->getMessageBag()
            ], Response::HTTP_BAD_REQUEST);
        else :
            $data = Vehicle::select('id', 'speed_limitation')->where('customer_id', Auth::user()->id)->findOrFail($id);
            $data->speed_limitation = $request->speed_limitation;
            $data->save();
            return Response([
                'status'    => true,
                'message'   => 'Customer Vehicle Speed Info',
                'data'      => array(
                    'id'                => $data->id,
                    'speed_limitation'  => $data->speed_limitation,
                )
            ], Response::HTTP_CREATED);
        endif;
    }

    /**
     * @OA\Get(
     *     path="/api/customer/ccustomer-vehicles-alert-setting/id",
     *     tags={"customer-vehicles-alert-setting"},
     *     summary="Get Customer Vehicle Alert Info By Id",
     *     description="",
     *     operationId="customer-vehicles-alert-setting",
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
    public function vehicleAlertSetting(string $id): Response
    {
        $data = Vehicle::where('customer_id', Auth::user()->id)->findOrFail($id);

        return Response([
            'status'    => true,
            'message'   => 'Customer Vehicle Alert Seeting',
            'data'      => array(
                'id'                        => $data->id,
                'notification_status'       => $data->notification_status,
                'email_status'              => $data->email_status,
                'over_speed_alert_status'   => $data->over_speed_alert_status,
                'range_alert_status'        => $data->range_alert_status,
                'sms_alert_status'          => $data->sms_alert_status,
            )
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/customer/ccustomer-vehicles-alert-setting/id",
     *     tags={"customer-vehicles-alert-setting-update"},
     *     summary="Update customer vehicle Alert info",
     *     description="",
     *     operationId="customer-vehicles-alert-setting-update",
     *     @OA\Parameter(
     *         name="notification_status",
     *         in="path",
     *         description="Notification Status in (0/1)",
     *         required=true,
     *         @OA\Schema(
     *             type="boolean"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="email_status",
     *         in="path",
     *         description="Email Status in (0/1)",
     *         required=true,
     *         @OA\Schema(
     *             type="boolean"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="over_speed_alert_status",
     *         in="path",
     *         description="Over Speed Alert Status in (0/1)",
     *         required=true,
     *         @OA\Schema(
     *             type="boolean"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="range_alert_status",
     *         in="path",
     *         description="Range Alert Status in (0/1)",
     *         required=true,
     *         @OA\Schema(
     *             type="boolean"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sms_alert_status",
     *         in="path",
     *         description="SMS Alert Status in (0/1)",
     *         required=true,
     *         @OA\Schema(
     *             type="boolean"
     *         )
     *     ),
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
    public function vehicleAlertSettingUpdate(Request $request, string $id)
    {
        $validator = validator(
            $request->all(),
            [
                'notification_status'       => 'required|numeric|in:0,1',
                'email_status'              => 'required|numeric|in:0,1',
                'over_speed_alert_status'   => 'required|numeric|in:0,1',
                'range_alert_status'        => 'required|numeric|in:0,1',
                'sms_alert_status'          => 'required|numeric|in:0,1',
            ],
            [
                'notification_status.required'      => 'Notification Status is Required',
                'notification_status.numeric'       => 'Provide Valid Notification Status',
                'notification_status.in'            => 'Provide Valid Notification Status',
                'email_status.required'             => 'Email Status is Required',
                'email_status.numeric'              => 'Provide Valid Email Status',
                'email_status.in'                   => 'Provide Valid Email Status',
                'over_speed_alert_status.required'  => 'Over Speed Alert Status is Required',
                'over_speed_alert_status.numeric'   => 'Provide Valid Over Speed Alert Status',
                'over_speed_alert_status.in'        => 'Provide Valid Over Speed Alert Status',
                'sms_alert_status.required'         => 'SMS Alert Status is Required',
                'sms_alert_status.numeric'          => 'Provide Valid SMS Alert Status',
                'sms_alert_status.in'               => 'Provide Valid SMS Alert Status',
            ]
        );
        if ($validator->fails()) :
            return Response([
                'status' => false,
                'message' => $validator->getMessageBag()->first(),
                'errors' => $validator->getMessageBag()
            ], Response::HTTP_BAD_REQUEST);
        else :
            $data = Vehicle::where('customer_id', Auth::user()->id)->findOrFail($id);

            $data->notification_status      = $request->notification_status;
            $data->email_status             = $request->email_status;
            $data->over_speed_alert_status  = $request->over_speed_alert_status;
            $data->range_alert_status       = $request->range_alert_status;
            $data->sms_alert_status         = $request->sms_alert_status;
            $data->save();

            return Response([
                'status'    => true,
                'message'   => 'Customer Vehicle Speed Info',
                'data'      => array(
                    'id'                        => $data->id,
                    'notification_status'       => $data->notification_status,
                    'email_status'              => $data->email_status,
                    'over_speed_alert_status'   => $data->over_speed_alert_status,
                    'range_alert_status'        => $data->range_alert_status,
                    'sms_alert_status'          => $data->sms_alert_status,
                )
            ], Response::HTTP_CREATED);
        endif;
    }
}
