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
            'id'        => $this->id,
            'purpose'   => $this->vehicle_purpose,
            'model'     => $this->vehicle_brand,
            'number'    => $this->registration_number,
            'type'      => $this->vehicleType->vehicle_type_name,
            'mileage'      => $this->vehicle_kpl,
            'fuel_congestion' => $this->fuel_capacity,
            'diver' => [
                'name'  => $this->driverInfo->userDetails->first_name . ' ' . $this->driverInfo->userDetails->last_name
            ]
        ];
    }
}
