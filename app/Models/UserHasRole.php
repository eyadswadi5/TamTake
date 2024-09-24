<?php

namespace App\Models;

class UserHasRole extends BaseModel
{
    protected $table = 'user_has_role';

    protected $fillable = [
        'user_id', 'role_id'
    ];
    
    // relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
