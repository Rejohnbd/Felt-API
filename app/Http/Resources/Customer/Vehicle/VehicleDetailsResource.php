<?php

namespace App\Http\Resources\Customer\Vehicle;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleDetailsResource extends JsonResource
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
            'id'                    => $this->id,
            'vehicle_purpose'       => $this->vehicle_purpose,
            'vehicle_brand'         => $this->vehicle_brand,
            'registration_number'   => $this->registration_number,
            'vehicle_type_name'     => $this->vehicleType->vehicle_type_name,
            'vehicle_kpl'           => $this->vehicle_kpl,
            'fuel_capacity'         => $this->fuel_capacity,
            'diver_info'            => [
                'first_name'        => $this->driverInfo->userDetails->first_name,
                'last_name'         => $this->driverInfo->userDetails->last_name,
                'phone_number'      => $this->driverInfo->userDetails->phone_number,
                'phone_optional'    => $this->driverInfo->userDetails->phone_optional,
                'image'             => $this->driverInfo->userDetails->image
            ]
        ];
    }
}
