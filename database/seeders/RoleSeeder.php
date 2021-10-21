<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['role_name'=>'SuperAdmin']);
        Role::create(['role_name'=>'Admin']);
        Role::create(['role_name'=>'User']);
        Role::create(['role_name'=>'Artist']);
        Role::create(['role_name'=>'Moderator']);
    }
}