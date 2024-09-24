<?php

namespace App\Models;

class Permission extends BaseModel
{
    protected $fillable = [
        'permission', 'desc', 'guard'
    ];
    
    // relationships
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions', 'permission_id', 'role_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_has_permissions', 'permission_id', 'user_id');
    }
}
