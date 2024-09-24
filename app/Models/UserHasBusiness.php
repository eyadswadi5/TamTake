<?php

namespace App\Models;

class UserHasBusiness extends BaseModel
{
    protected $fillable = [
        'manager_id', 'business_name', 'business_registration_number', 
        'website', 'industry_type', 'account_status'
    ];

    protected $table = "user_has_business";

    // relationhsips
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}
