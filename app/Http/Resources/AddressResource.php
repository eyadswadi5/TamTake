<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'street_address' => $this->street_address,
            'apartment_number' => $this->apartment_number,
            'country' => $this->country,
            'city' => $this->city,
            'region' => $this->region,
            'zip_code' => $this->zip_code,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
        ];
    }
}
