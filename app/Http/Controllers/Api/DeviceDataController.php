<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceData;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DeviceDataController extends Controller
{
    public function saveDeviceData(Request $request)
    {
        // $device_data_json = trim(file_get_contents("php://input"));
        // $data = json_decode($device_data_json);

        // $imei           = $request->imei;
        // $latitude       = $request->lat;
        // $longitude      = $request->lng;
        // $engine_status  = $request->status;
        // $rotation       = $request->rotation;
        // $speed          = $request->speed;
        // $device_time    = $request->device_time;

        $deviceInfo = Device::with('vehicle:id,device_id')->select('id', 'device_imei')->where('device_imei', $request->imei)->first();

        if (!is_null($deviceInfo)) :
            DeviceData::create([
                'vehicle_id'    => $deviceInfo->vehicle->id,
                'device_id'     => $deviceInfo->id,
                'device_imei'   => $deviceInfo->device_imei,
                'latitude'      => $request->lat,
                'longitude'     => $request->lng,
                'engine_status' => $request->status,
                'rotation'      => $request->rotation,
                'speed'         => $request->speed,
                'device_time'   => $request->device_time,
                'json_data'     => json_encode($request->all()),
            ]);
            return Response([
                'status'    => true,
                'message'   => 'Data Save Successflly',
            ], Response::HTTP_CREATED);
        else :
            return Response([
                'status'    => true,
                'message'   => 'Data Save Successflly',
            ], Response::HTTP_BAD_REQUEST);
        endif;
    }
}
