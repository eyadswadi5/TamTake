<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'origin_address' => new AddressResource($this->originAddress),
            'destination_address' => new AddressResource($this->destinationAddress),
            'packages' => PackageResource::collection($this->packages),
            'shipment_date' => $this->shipment_date,
            'delivery_date' => $this->delivery_date,
            'status' => $this->status,
            'shipping_method' => $this->shipping_method,
            'tracking_number' => $this->tracking_number,
            'total_weight' => $this->total_weight,
            'total_value' => $this->total_value,
            'insurance' => $this->insurance,
            'courier_id' => $this->courier_id,
            'special_instructions' => $this->special_instructions,
        ];
    }
}
