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
            'id'                    => $this->driverInfo->id,
            'email'                 => $this->driverInfo->email,
            'first_name'            => $this->driverInfo->userDetails->first_name,
            'last_name'             => $this->driverInfo->userDetails->last_name,
            'phone_number'          => $this->driverInfo->userDetails->phone_number,
            'phone_number'          => $this->driverInfo->userDetails->phone_number,
            'phone_optional'        => $this->driverInfo->userDetails->phone_optional,
            'image'                 => $this->driverInfo->userDetails->image,
            'registration_number'   => $this->registration_number,
            'vehicle_brand'         => $this->vehicle_brand,
            'vehicle_model_year'    => $this->vehicle_model_year,
            'vehicle_type_name'     => $this->vehicleType->vehicle_type_name,
            'vehicle_type_image'    => $this->vehicleType->vehicle_type_image
        ];
    }
}
