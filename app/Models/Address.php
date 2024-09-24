<?php

namespace App\Models;

class Address extends BaseModel
{
    protected $fillable = [
        'customer_id', 'street_address', 'apartment_number', 'country', 
        'city', 'region', 'zip_code', 'latitude', 'longitude', 'is_primary'
    ];
    
    // relationships
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function shipmentsFrom()
    {
        return $this->hasMany(Shipment::class, 'origin_address_id');
    }

    public function shipmentsTo()
    {
        return $this->hasMany(Shipment::class, 'destination_address_id');
    }
}
