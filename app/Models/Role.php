<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model

{
    use HasFactory, SoftDeletes;
    /* Role to User relationship */
    public function user()
    {
        return $this->hasOne(User::class);
    }
    public static function get_role_id($id)
    {
        $roles =  Role::where('id', $id)->first();
        if (!isset($roles))
            $roles =  Role::where('role_name', USER_ROLE_USER)->first();

        return $roles->id;
    }
}
