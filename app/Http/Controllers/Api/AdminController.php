<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Rules\StrongPassword;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /* New Admin*/
    public function add(Request $request)
    {
        $data = [];
        $user_data = [];
        $message = __('user.admin_add_failed');
        $status_code = BADREQUEST;

        $validate_data = Validator::make($request->all(), [
            'email' => 'email|required|unique:users',
            'password' => [new StrongPassword],
        ]);

        if ($validate_data->fails()) {
            $errors = $validate_data->errors();
            $message =  implode(', ', $errors->all());
        } else {
            $data['role_id'] = $request->role;
            $data['name'] = $request->name;
            $data['email'] = $request->email;
            $data['password'] = bcrypt($request->password);
          
            $inserted_data = User::create($data);

            $users= User::select(
                'id',
                'role_id',
                'name',
                'email',
            )->where('id', $inserted_data->id)->get()->first();
            $message = __('user.admin_add_success');
            $status_code = SUCCESSCODE;
        }
        
        return response([
            'data' => $users,
            'message' => $message,
            'status_code' => $status_code,
        ], $status_code);
    }

    /*Admin Edit*/
    public function update(Request $request, $admin_id)
    {
        $data = [];
        $message     =  '';
        $status_code = '';
  
        $user_data = User::find($admin_id);

           
        if ($user_data) {
            try {
                $update = [];
                if (isset($request->role)) {
                    $update['role_id'] = $request->role;
                }
                if (isset($request->name)) {
                    $update['name'] = $request->name;
                }
                if (isset($request->email)) {
                    $update['email'] = $request->email;
                }

                $update['updated_at'] = date("Y-m-d H:i:s");

                if ($user_data->role_id==1) {
                    if (count($update) != 0) {
                        DB::table('users')->where('id', $admin_id)->update($update);
                    }
                    $data= User::select(
                        'id',
                        'role_id',
                        'name',
                        'email',
                    )->where('id', $admin_id)->get()->first();
                    $message = __('user.update_success');
                    $status_code = SUCCESSCODE;
                } else {
                    if ($user_data->role_id==2) {
                        if (count($update) != 0) {
                            DB::table('users')->where('id', $admin_id)->update($update);
                        }
                        $data= User::select(
                            'id',
                            'role_id',
                            'name',
                            'email',
                        )->where('id', $admin_id)->get()->first();
                        $message = __('user.update_success');
                        $status_code = SUCCESSCODE;
                    } else {
                        $message = __('user.admin_failed');
                        $status_code = BADREQUEST;
                    }
                }
            } catch (Exception $e) {
                $data=[];
                $message = __('user.update_failed') . ' ' . $e->getMessage();
                $status_code = BADREQUEST;
            }
        
        }

        return response([
             'data'        => $data,
             'message'     => $message,
             'status_code' => $status_code
         ], $status_code);
    }

     /*Admin Listing*/
     public function list(Request $request)
     {
         $user_data = [];
         $report_data = [];
         $message =  __('user.user_list_failed');
         $status_code = BADREQUEST;
  
         $user_data = User::active_admin_list($request->input('role'), $request);
         $message = __('user.user_list_success');
         $status_code = SUCCESSCODE;
 
         return response([
             'data'        => $user_data,
             'message'     => $message,
             'status_code' => $status_code
         ], $status_code);
     }

    /*Admin Delete*/
    public function delete(Request $request)
    {
        $user_data = [];
        $data      = [];
        $message =  __('user.invalid_user');
        $status_code = BADREQUEST;
     
        $user_data = User::select(
            DB::raw('role_name AS role'),
            DB::raw('users.id AS id'),
        )->leftJoin('roles', 'users.role_id', '=', 'roles.id')
         ->where('role_name', '!=', 'User')
         ->where(DB::raw('users.id'), '=', $request->id)->delete();
       
        if ($user_data === 1) {
            $message = __('user.admin_delete_success');
            $status_code = SUCCESSCODE;
        } else {
            $message = __('user.admin_failed');
            $status_code = BADREQUEST;
        }

        return response([
            'data'        => $data,
            'message'     => $message,
            'status_code' => $status_code
        ], $status_code);
    }
}
