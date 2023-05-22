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
}
