<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VhicleShortListResource extends JsonResource
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
            'vehicle_brand'         => $this->vehicle_brand,
            'vehicle_type_name'     => $this->vehicleType->vehicle_type_name,
            'registration_number'   => $this->registration_number,
            'vehicle_purpose'       => $this->vehicle_purpose
        ];
    }
}
