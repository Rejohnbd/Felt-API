<?php

namespace App\Http\Resources\Customer\Driver;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SingleDriverResouce extends JsonResource
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
            'id'                => $this->id,
            'email'             => $this->email,
            'first_name'        => $this->userDetails->first_name,
            'last_name'         => $this->userDetails->last_name,
            'phone_number'      => $this->phone_number,
            'phone_optional'    => $this->userDetails->phone_optional,
            'image'             => $this->userDetails->image,
            'vehicle_id'        => $this->userDetails->vehicle ? $this->userDetails->vehicle->id : null,
        ];
    }
}
