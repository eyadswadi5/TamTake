<?php

namespace App\Models;

class Destination extends BaseModel
{
    protected $fillable = [
        'street_address', 'apartment_number', 'country', 
        'city', 'region', 'zip_code', 'latitude', 'longitude'
    ];
    
    // relationships
    public function shipmentsTo()
    {
        return $this->hasMany(Shipment::class, 'destination_address_id');
    }
}
