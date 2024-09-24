<?php

namespace App\Models;


class RoleHasPermission extends BaseModel
{
    protected $fillable = [
        'role_id', 'permission_id'
    ];
    
    // relationships
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }
}
