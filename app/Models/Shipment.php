<?php

namespace App\Models;
use Illuminate\Support\Str;

class Shipment extends BaseModel
{
    protected $fillable = [
        'customer_id', 'origin_address_id', 'destination_address_id', 'shipment_date',
        'delivery_date', 'status', 'shipping_method', 'tracking_number', 'total_weight', 
        'total_value', 'insurance', 'courier_id', 'special_instructions'
    ];

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['id', 'customer_id', 'origin_address_id', 'destination_address_id', 'courier_id'];
    }

    public static function generateUniqueTrackingNumber()
    {
        do {
            // Generate a random string
            $trackingNumber = 'TRK-' . strtoupper(Str::random(10));

            // Check if it's already in use
        } while (self::where('tracking_number', $trackingNumber)->exists());

        return $trackingNumber;
    }

    // relationships
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function originAddress()
    {
        return $this->belongsTo(Address::class, 'origin_address_id');
    }

    public function destinationAddress()
    {
        return $this->belongsTo(Destination::class, 'destination_address_id');
    }

    public function courier()
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    public function packages()
    {
        return $this->hasMany(Package::class, 'shipment_id');
    }

}
