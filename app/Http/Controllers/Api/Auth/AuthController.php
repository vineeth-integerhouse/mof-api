<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Rules\StrongPassword;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = [];
        $user_data = [];
        $status_code = [];

        $validate_data = Validator::make($request->all(), [
            'email' => 'email|required|unique:users',
            'password' => ['required', new StrongPassword],
        ]);

        if ($validate_data->fails()) {
            $errors = $validate_data->errors();
            $message = implode(', ', $errors->all());
            $status_code = BADREQUEST;
        } else {
            $user_data['email'] = $request->email;
            $user_data['password'] = bcrypt($request->password);

            if (isset($request->role)) {
                $user_data['role_id'] = $request->role;
            } else {
                $user_data['role_id'] = Role::get_role_id(USER_ROLE_USER);
            }

            $inserted_data = User::create($user_data);

            $data= User::select(
                'id',
                'email',
            )->where('id', $inserted_data->id)->get()->first();

            $message = __('user.register_success');
            $status_code = SUCCESSCODE;
        }

        return response([
            'data' => $data,
            'message' => $message,
            'status_code' => $status_code
        ], $status_code);
    }

    public function login(Request $request)
    {
        $data = [];
        $login_data = [];

        $validate_data = Validator::make($request->all(), [
            'email' => 'email|required',
            'password' => 'required',
        ]);

        if ($validate_data->fails()) {
            $errors = $validate_data->errors();
            $message = implode(', ', $errors->all());
            $status_code = BADREQUEST;
        } else {
            $login_data['email'] = $request->email;
            $login_data['password'] = $request->password;

            if (auth()->attempt($login_data)) {
                $user= User::select(
                    'id',
                    'email',
                    'role_id'
                )->with('role', function ($query) {
                    $query->select('id', 'role_name');
                })->where('email', $request->email)->first();
                      
                $access_token = $user->createToken('authToken')->accessToken;
                if ($user->role->role_name == USER_ROLE_SUPERADMIN) {
                    $data = ['access_token' => $access_token, 'user' => $user];
                } elseif ($user->role->role_name == USER_ROLE_ADMIN) {
                    $data = ['access_token' => $access_token, 'user' => $user];
                
                } elseif ($user->role->role_name == USER_ROLE_MODERATOR) {
                    $data = ['access_token' => $access_token, 'user' => $user];
                } else {
                    $data = ['access_token' => $access_token, 'user' => $user];
                }
                $message = __('auth.login_success');
                $status_code = SUCCESSCODE;
            } else {
                $message = __('auth.login_failed');
                $status_code = BADREQUEST;
            }
        }

        return response([
            'data' => $data,
            'message' => $message,
            'status_code' => $status_code
        ], $status_code);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response([
            'data' => [],
            'message' => __('auth.logout_success'),
            'status_code' => SUCCESSCODE
        ]);
    }
}