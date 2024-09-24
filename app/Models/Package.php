<?php

namespace App\Models;

class Package extends BaseModel
{
    protected $fillable = [
        'shipment_id', 'weight', 'dimensions', 'value', 'package_type', 
        'is_fragile', 'hazardous_materials', 'barcode', 'special_instruction'
    ];
    
    // relationships
    public function shipment()
    {
        return $this->belongsTo(Shipment::class, 'shipment_id');
    }

}
