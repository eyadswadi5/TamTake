<?php

namespace App\Models;

class UserHasPermission extends BaseModel
{
    protected $fillable = [
        'user_id', 'permission_id'
    ];
    
    // relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }
}
