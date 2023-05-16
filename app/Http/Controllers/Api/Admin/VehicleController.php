<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $data = Vehicle::with([
            'customerInfo.userDetails',
            'vehicleType',
            'servicePackage',
            'deviceInfo.deviceType'
        ])->get();
        return Response(['status' => true, 'message' => 'All Vehicle', 'data' => $data], Response::HTTP_OK);
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
    public function store(Request $request): Response
    {
        $validator = validator(
            $request->all(),
            [
                'customer_id'               => 'required|numeric|exists:users,id',
                'vehicle_type_id'           => 'required|numeric|exists:vehicle_types,id',
                'service_package_id'        => 'required|numeric|exists:service_packages,id',
                'device_id'                 => 'nullable|numeric|exists:devices,id',
                'registration_number'       => 'required|string',
                'registration_expire_date'  => 'required|date_format:Y-m-d',
                'insurance_expire_date'     => 'required|date_format:Y-m-d',
                'tax_token_expire_date'     => 'required|date_format:Y-m-d',
                'fuel_capacity'             => 'required|numeric|gt:0',
                'vehicle_kpl'               => 'required|numeric|gt:0',
                'payment_status'            => 'required|numeric|in:0,1',
                'service_status'            => 'required|numeric|in:0,1',
            ],
            [
                'customer_id.required'                  => 'Customer is Required',
                'customer_id.numeric'                   => 'Provide Valid Customer',
                'customer_id.exists'                    => 'Provide Valid Customer',
                'vehicle_type_id.required'              => 'Vehicle is Required',
                'vehicle_type_id.numeric'               => 'Provide Valid Vehicle Type',
                'vehicle_type_id.exists'                => 'Provide Valid Vehicle Type',
                'service_package_id.required'           => 'Service Package is Required',
                'service_package_id.numeric'            => 'Provide Valid Service Package',
                'service_package_id.exists'             => 'Provide Valid Service Package',
                'device_id.numeric'                     => 'Provide Valid Device',
                'device_id.exists'                      => 'Provide Valid Device',
                'registration_number.required'          => 'Registration Number is Required',
                'registration_number.string'            => 'Provide Valid Registration Number',
                'registration_expire_date.required'     => 'Registration Expire Date is Required',
                'registration_expire_date.date_format'  => 'Registration Expire Date format must be YYYY-MM-DD',
                'insurance_expire_date.required'        => 'Insurance Expire Date is Required',
                'insurance_expire_date.date_format'     => 'Insurance Expire Date format must be YYYY-MM-DD',
                'tax_token_expire_date.required'        => 'Tax Token Expire Date is Required',
                'tax_token_expire_date.date_format'     => 'Tax Token Expire Date format must be YYYY-MM-DD',
                'tax_token_expire_date.date_format'     => 'Tax Token Expire Date format must be YYYY-MM-DD',
                'fuel_capacity.required'                => 'Vehicle Fuel Capacity Required',
                'fuel_capacity.numeric'                 => 'Provide Valid Vehicle Fuel Capacity',
                'fuel_capacity.gt'                      => 'Provide Valid Vehicle Fuel Capacity',
                'vehicle_kpl.required'                  => 'Vehicle Milage Required',
                'vehicle_kpl.numeric'                   => 'Provide Valid Vehicle Milage',
                'vehicle_kpl.gt'                        => 'Provide Valid Vehicle Milage',
                'payment_status.required'               => 'Vehicle Payment Status is Required',
                'payment_status.numeric'                => 'Provide Valid Vehicle Payment Status',
                'payment_status.in'                     => 'Provide Valid Vehicle Payment Status',
                'service_status.required'               => 'Vehicle Service Status is Required',
                'service_status.numeric'                => 'Provide Valid Vehicle Service Status',
                'service_status.in'                     => 'Provide Valid Vehicle Service Status',
            ]
        );
        if ($validator->fails()) :
            return Response([
                'status'    => false,
                'message'   => $validator->getMessageBag()->first(),
                'errors'    => $validator->getMessageBag()
            ], Response::HTTP_BAD_REQUEST);
        else :
            DB::beginTransaction();
            try {
                $newVehicle = new Vehicle;
                $newVehicle->customer_id                = $request->customer_id;
                $newVehicle->vehicle_type_id            = $request->vehicle_type_id;
                $newVehicle->service_package_id         = $request->service_package_id;
                $newVehicle->driver_id                  = $request->customer_id;
                $newVehicle->device_id                  = $request->device_id;
                $newVehicle->registration_number        = $request->registration_number;
                $newVehicle->registration_expire_date   = $request->registration_expire_date;
                $newVehicle->insurance_expire_date      = $request->insurance_expire_date;
                $newVehicle->tax_token_expire_date      = $request->tax_token_expire_date;
                $newVehicle->vehicle_brand              = $request->vehicle_brand;
                $newVehicle->vehicle_model_year         = $request->vehicle_model_year;
                $newVehicle->fuel_capacity              = $request->fuel_capacity;
                $newVehicle->vehicle_kpl                = $request->vehicle_kpl;
                $newVehicle->payment_status             = $request->payment_status;
                $newVehicle->service_status             = $request->service_status;
                if (!empty($request->device_id)) :
                    $newVehicle->installation_status    = 1;
                endif;
                $newVehicle->save();

                DB::commit();

                return Response([
                    'status'    => true,
                    'message'   => 'Vehicle Created Successfully.',
                    'data'      => $newVehicle
                ], Response::HTTP_CREATED);
            } catch (\Exception $e) {
                DB::rollback();
                return Response([
                    'status'    => false,
                    'message'   => $e->getMessage(),
                    'errors'    => []
                ], Response::HTTP_BAD_REQUEST);
            }

        endif;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        //
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
