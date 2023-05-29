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
}
