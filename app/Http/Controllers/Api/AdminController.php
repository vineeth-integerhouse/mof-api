<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Rules\StrongPassword;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
    public function delete(Request $request, $admin_id)
    {
        $user_data = [];
        $data      = [];
        $message =  __('user.invalid_user');
        $status_code = BADREQUEST;
     
        $user = User::where('id', $admin_id)->first();

        $user_data = User::select(
            DB::raw('role_name AS role'),
            DB::raw('users.id AS id'),
        )->leftJoin('roles', 'users.role_id', '=', 'roles.id')
         ->where('role_name', '!=', 'User')
         ->where(DB::raw('users.id'), '=', $admin_id)->delete();
 
        if ($user_data === 1) {
            $data['id']   = $user->id;
            $data['email'] = $user->email;
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

    /*Admin Settings */

    public function settings(Request $request)
    {
        $data = [];
        $error = [];
        $users_count = 0;
        $existing_password_check = true;
        $password_confirmation = true;
        $message = __('user.update_failed');
        $status_code = BADREQUEST;
   
        $current_user=get_user();
   
        if ($current_user) {
            try {
                $update = [];
                if (isset($request->password) && isset($request->password_confirmation)) {
                    if ($request->password === $request->password_confirmation) {
                        $update['password'] = bcrypt($request->password);
                    } else {
                        $password_confirmation = false;
                    }
                }
                if (isset($request->email)) {
                    $update['email'] = $request->email;
                    $users_count = User::withTrashed()->where('email', $request->email)->where('id', '!=', $current_user->id)->count();
                }
                if ($users_count == 0 && $password_confirmation == true) {
                    if (isset($request->name)) {
                        $update['name'] = $request->name;
                    }
               
                    $update['updated_at'] = date("Y-m-d H:i:s");
                    if (count($update) != 0) {
                        DB::table('users')->where('id', $current_user->id)->update($update);
                    }
                    $message = __('user.user_settings');
                    $status_code = SUCCESSCODE;
   
                    $data= User::select(
                        'id',
                        'name',
                        'email',
                        'profile_pic'
                    )->where('id', $current_user->id)->get()->first();
                }
                $error = [];
                if ($users_count!=0) {
                    $data=[];
                 
                    array_push($error, array('type'=>'email','message'=> __('user.email_in_use')));
                    $status_code = BADREQUEST;
                }
                if ($password_confirmation == false) {
                    $data=[];
                    array_push($error, array('type'=>'password_confirmation','message'=> __('user.password_not_match')));
                    $status_code = BADREQUEST;
                }
            } catch (Exception $e) {
                $message = __('user.user_settings_failed') . ' ' . $e->getMessage();
                $status_code = BADREQUEST;
            }
        }
              
        return response([
         'data' => $data,
         'error' => $error,
         'message' => $message,
         'status_code' => $status_code,
       ], $status_code);
    }
}