<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $data = Device::with('deviceType')->orderBy('id', 'DESC')->get();
        return Response([
            'status'    => true,
            'message'   => 'All Devices',
            'data'      => $data
        ], Response::HTTP_OK);
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
                'device_imei'           => 'required|unique:devices,device_imei',
                'device_type_id'        => 'required|numeric|exists:device_types,id',
                'device_sim'            => 'required|min:11|max:11|unique:devices,device_sim',
                'device_sim_type'       => 'required|numeric|in:1,2',
                'device_health_status'  => 'required|numeric|in:0,1,2',
            ],
            [
                'device_imei.required'          => 'Device IMEI No is required',
                'device_imei.unique'            => 'Device IMEI No Already Exits',
                'device_type_id.required'       => 'Device Type is required',
                'device_type_id.numeric'        => 'Provide Valid Device Type',
                'device_type_id.exists'         => 'Provide Valid Device Type',
                'device_sim.required'           => 'Device SIM Number is Required',
                'device_sim.min'                => 'Device SIM Number not Less than 11',
                'device_sim.max'                => 'Device SIM Number not Greater than 11',
                'device_sim_type.required'      => 'Device SIM Type is Required',
                'device_sim_type.numeric'       => 'Provide Valid Device SIM Type',
                'device_sim_type.in'            => 'Provide Valid Device SIM Type',
                'device_sim_type.unique'        => 'Device SIM Number Already Exist',
                'device_health_status.required' => 'Device Health Status is Required',
                'device_health_status.numeric'  => 'Provide Valid Device Health Status',
                'device_health_status.in'       => 'Provide Valid Device Health Status',
            ]
        );
        if ($validator->fails()) :
            return Response([
                'status'    => false,
                'message'   => $validator->getMessageBag()->first(),
                'errors'    => $validator->getMessageBag()
            ], Response::HTTP_BAD_REQUEST);
        else :

            $newDevice = new Device;
            $newDevice->device_imei             = $request->device_imei;
            $newDevice->device_type_id          = $request->device_type_id;
            $newDevice->device_sim              = $request->device_sim;
            $newDevice->device_sim_type         = $request->device_sim_type;
            $newDevice->device_health_status    = $request->device_health_status;
            $newDevice->save();

            return Response([
                'status'    => true,
                'message'   => 'Device Created Successfully.',
                'data'      => $newDevice
            ], Response::HTTP_CREATED);
        endif;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        $deviceInfo = Device::with('deviceType')->find($id);
        if (!is_null($deviceInfo)) :
            return Response([
                'status'    => true,
                'message'   => 'Device Info.',
                'data'      => $deviceInfo
            ], Response::HTTP_OK);
        else :
            return Response([
                'status'    => false,
                'message'   => 'Device Not Found.',
                'data'      => $deviceInfo
            ], Response::HTTP_NOT_FOUND);
        endif;
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
    public function update(Request $request, string $id): Response
    {
        $deviceInfo = Device::with('deviceType')->find($id);
        if (!is_null($deviceInfo)) :
            $validator = validator(
                $request->all(),
                [
                    'device_imei'           => 'required|unique:devices,device_imei,' . $deviceInfo->id,
                    'device_type_id'        => 'required|numeric|exists:device_types,id',
                    'device_sim'            => 'required|min:11|max:11|unique:devices,device_sim,' . $deviceInfo->id,
                    'device_sim_type'       => 'required|numeric|in:1,2',
                    'device_health_status'  => 'required|numeric|in:0,1,2',
                ],
                [
                    'device_imei.required'          => 'Device IMEI No is required',
                    'device_imei.unique'            => 'Device IMEI No Already Exits',
                    'device_type_id.required'       => 'Device Type is required',
                    'device_type_id.numeric'        => 'Provide Valid Device Type',
                    'device_type_id.exists'         => 'Provide Valid Device Type',
                    'device_sim.required'           => 'Device SIM Number is Required',
                    'device_sim.min'                => 'Device SIM Number not Less than 11',
                    'device_sim.max'                => 'Device SIM Number not Greater than 11',
                    'device_sim_type.required'      => 'Device SIM Type is Required',
                    'device_sim_type.numeric'       => 'Provide Valid Device SIM Type',
                    'device_sim_type.in'            => 'Provide Valid Device SIM Type',
                    'device_sim_type.unique'        => 'Device SIM Number Already Exist',
                    'device_health_status.required' => 'Device Health Status is Required',
                    'device_health_status.numeric'  => 'Provide Valid Device Health Status',
                    'device_health_status.in'       => 'Provide Valid Device Health Status',
                ]
            );
            if ($validator->fails()) :
                return Response([
                    'status'    => false,
                    'message'   => $validator->getMessageBag()->first(),
                    'errors'    => $validator->getMessageBag()
                ], Response::HTTP_BAD_REQUEST);
            else :
                $deviceInfo->device_imei             = $request->device_imei;
                $deviceInfo->device_type_id          = $request->device_type_id;
                $deviceInfo->device_sim              = $request->device_sim;
                $deviceInfo->device_sim_type         = $request->device_sim_type;
                $deviceInfo->device_health_status    = $request->device_health_status;
                $deviceInfo->save();

                return Response([
                    'status'    => true,
                    'message'   => 'Device Updated Successfully.',
                    'data'      => $deviceInfo
                ], Response::HTTP_CREATED);
            endif;
        else :
            return Response([
                'status'    => false,
                'message'   => 'Device Not Found.',
                'data'      => $deviceInfo
            ], Response::HTTP_NOT_FOUND);
        endif;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): Response
    {
        $deviceInfo = Device::with('deviceType')->find($id);
        if (!is_null($deviceInfo)) :
            $deviceInfo->delete();
            return Response([
                'status'    => true,
                'message'   => 'Device Deleted Successfully.',
                'data'      => $deviceInfo
            ], Response::HTTP_ACCEPTED);
        else :
            return Response([
                'status'    => false,
                'message'   => 'Device Not Found.',
                'data'      => $deviceInfo
            ], Response::HTTP_NOT_FOUND);
        endif;
    }
}
