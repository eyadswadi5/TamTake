<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'weight' => $this->weight,
            'dimensions' => $this->dimensions,
            'value' => $this->value,
            'package_type' => $this->package_type,
            'is_fragile' => $this->is_fragile,
            'hazardous_materials' => $this->hazardous_materials,
            'barcode' => $this->barcode,
            'special_instruction' => $this->special_instruction,
        ];
    }
}
