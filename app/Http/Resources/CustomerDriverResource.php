<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerDriverResource extends JsonResource
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
            'email'                 => $this->email,
            'first_name'            => $this->userDetails->first_name,
            'last_name'             => $this->userDetails->last_name,
            'phone_number'          => $this->phone_number,
            'phone_optional'        => $this->userDetails->phone_optional,
            'image'                 => $this->userDetails->image,
            'registration_number'   => $this->userDetails->vehicle ? $this->userDetails->vehicle->registration_number : null,
            'vehicle_brand'         => $this->userDetails->vehicle ? $this->userDetails->vehicle->vehicle_brand : null,
            'vehicle_model_year'    => $this->userDetails->vehicle ? $this->userDetails->vehicle->vehicle_model_year : null,
            'vehicle_type_name'     => $this->userDetails->vehicle ? $this->userDetails->vehicle->vehicleType->vehicle_type_name : null,
            'vehicle_type_image'    => $this->userDetails->vehicle ? $this->userDetails->vehicle->vehicleType->vehicle_type_image : null
        ];
    }
}
