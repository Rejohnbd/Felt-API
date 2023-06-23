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

        $deviceInfo = Device::with(['vehicle:id,device_id,vehicle_kpl', 'deviceLatestData:device_id,latitude,longitude,fuel_use'])
            ->select('id', 'device_imei')
            ->where('device_imei', $request->imei)->first();

        if (!is_null($deviceInfo)) :
            if (is_null($deviceInfo->deviceLatestData)) :
                DeviceData::create([
                    'vehicle_id'    => $deviceInfo->vehicle->id,
                    'device_id'     => $deviceInfo->id,
                    'device_imei'   => $deviceInfo->device_imei,
                    'latitude'      => $request->lat,
                    'longitude'     => $request->lng,
                    'engine_status' => $request->status,
                    'rotation'      => $request->rotation,
                    'speed'         => $request->speed,
                    'distance'      => 0.000,
                    'fuel_use'      => 0.000,
                    'device_time'   => $request->device_time,
                    'json_data'     => json_encode($request->all())
                ]);
            else :
                $distance = $this->calculateDistance($deviceInfo->deviceLatestData->latitude, $deviceInfo->deviceLatestData->longitude, $request->lat, $request->lng);
                $fuel_use = (1 / $deviceInfo->vehicle->vehicle_kpl) * $distance;
                DeviceData::create([
                    'vehicle_id'    => $deviceInfo->vehicle->id,
                    'device_id'     => $deviceInfo->id,
                    'device_imei'   => $deviceInfo->device_imei,
                    'latitude'      => $request->lat,
                    'longitude'     => $request->lng,
                    'engine_status' => $request->status,
                    'rotation'      => $request->rotation,
                    'speed'         => $request->speed,
                    'distance'      => $distance,
                    'fuel_use'      => $fuel_use,
                    'device_time'   => $request->device_time,
                    'json_data'     => json_encode($request->all())
                ]);
            endif;

            return Response([
                'status'    => true,
                'message'   => 'Data Save Successflly',
            ], Response::HTTP_CREATED);
        else :
            return Response([
                'status'    => true,
                'message'   => 'Data Not Saved',
            ], Response::HTTP_BAD_REQUEST);
        endif;
    }

    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $pi80 = M_PI / 180;
        $lat1 *= $pi80;
        $lon1 *= $pi80;
        $lat2 *= $pi80;
        $lon2 *= $pi80;
        $r = 6372.797; // mean radius of Earth in km 
        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;
        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $km = $r * $c;
        //echo ' '.$km; 
        return $km;
    }
}
