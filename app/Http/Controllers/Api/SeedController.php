<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;

class SeedController extends Controller
{
    public function role(Request $request)
    {
        $data = [];
        $message = __('seeder.role_fail');
        $status_code = BADREQUEST;

        $role_details = Role::select('id', 'role_name')->get();

        if (isset($role_details)) {
            $message = __('seeder.role');
            $status_code = SUCCESSCODE;
            $data = $role_details;
        }
        return response([
            'data' => $data,
            'message' => $message,
            'status_code' => $status_code
        ], $status_code);
    }
}