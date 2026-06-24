<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasFactory;

    protected $table = 'tbl_admin';
    protected $guarded = array();

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'id' => 'integer',
        'user_name' => 'string',
        'email' => 'string',
        'password' => 'string',
        'role' => 'string',
        'permissions' => 'array',
        'status' => 'integer',
    ];

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function hasAccess(string $routeName): bool
    {
        return \App\Http\Middleware\RoleMiddleware::adminHasAccess(
            $this->role, $routeName, $this->permissions
        );
    }
}
