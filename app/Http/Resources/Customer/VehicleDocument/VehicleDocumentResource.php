<?php

namespace App\Http\Resources\Customer\VehicleDocument;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleDocumentResource extends JsonResource
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
            'id'                        => $this->id,
            'registration_number'       => $this->registration_number,
            'registration_number'       => $this->registration_number,
            'documents'                 => $this->documents
        ];
    }
}
