<?php

namespace App\Http\Resources\Customer\Report;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LiveTrackingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'registration_number'   => $this->registration_number,
            'vehicle_brand'         => $this->vehicle_brand,
            'vehicle_purpose'       => $this->vehicle_purpose,
            'vehicle_type_name'     => $this->vehicleType->vehicle_type_name,
            'driver_first_name'     => $this->driverInfo->userDetails->first_name,
            'driver_last_name'      => $this->driverInfo->userDetails->last_name,
            'driver_phone_number'   => $this->driverInfo->phone_number,
            'latitude'              => $this->vehicleLatestData ? $this->vehicleLatestData->latitude : null,
            'longitude'             => $this->vehicleLatestData ? $this->vehicleLatestData->longitude : null,
            'engine_status'         => $this->vehicleLatestData ? $this->vehicleLatestData->engine_status : null,
            'rotation'              => $this->vehicleLatestData ? $this->vehicleLatestData->rotation : null,
            'speed'                 => $this->vehicleLatestData ? $this->vehicleLatestData->rotation : null
        ];
    }
}
