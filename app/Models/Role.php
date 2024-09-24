<?php

namespace App\Models;

class Role extends BaseModel
{
    protected $fillable = ['role', 'desc'];

    public function getPermissions() {
        $permissions = Permission::join("role_has_permissions","permissions.id", "=", "role_has_permissions.permission_id")
            ->where("role_has_permissions.role_id", "=", $this->id)
            ->select("permissions.id", "permissions.permission", "permissions.guard")
            ->get();

        return $permissions;
    }

    // relationships
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions', 'role_id', 'permission_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_has_role', 'role_id', 'user_id');
    }
}
