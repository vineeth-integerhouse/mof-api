<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
 use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    
    public function google_login(Request $request)
    {
        $message='';
        $status_code='';

        $user = Socialite::driver('google')->userFromToken($request->token);

        $find_user= User::select(
            'id',
            'email',
            'role_id',
            'google_id'


        )->with('role', function ($query) {
                $query->select('id', 'role_name');
            })->where('email',$user->email)->first();
      
        if ($find_user) {
            $access_token = $find_user->createToken('authToken')->accessToken;
            $data = ['access_token' => $access_token, 'user' => $find_user];
            $message="Google Login";
            $status_code=SUCCESSCODE;
        } else {
            User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'google_id'=> $user->id,
                    'role_id'=> $request->role,
                    'password' => encrypt('my-google')
                ]);
     
            $user= User::select(
                'id',
                'email',
                'role_id'
            )->with('role', function ($query) {
                    $query->select('id', 'role_name');
                })->where('email', $user->email)->first();
              
            $access_token = $user->createToken('authToken')->accessToken;
            $data = ['access_token' => $access_token, 'user' => $user];
            $message="Google Login";
            $status_code=SUCCESSCODE;
        }
    
        return response([
            'data' => $data,
            'message' => $message,
            'status_code' => $status_code
        ], $status_code);
    }

    public function facebook_login(Request $request)
    {
        $message='';
        $status_code='';

        $user = Socialite::driver('facebook')->userFromToken($request->token);

        $find_user= User::select(
            'id',
            'email',
            'role_id',
            'facebook_id'
        )->with('role', function ($query) {
                $query->select('id', 'role_name');
            })->where('email',$user->email)->first();

        if ($find_user) {
            $access_token = $find_user->createToken('authToken')->accessToken;
            $data = ['access_token' => $access_token, 'user' => $find_user];
            $message="Facebook Login";
            $status_code=SUCCESSCODE;
        } else {
            User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'facebook_id'=> $user->id,
                    'role_id'=> $request->role,
                    'password' => encrypt('my-facebook')
                ]);

            $user= User::select(
                'id',
                'email',
                'role_id'
            )->with('role', function ($query) {
                    $query->select('id', 'role_name');
                })->where('email', $user->email)->first();

            $access_token = $user->createToken('authToken')->accessToken;
            $data = ['access_token' => $access_token, 'user' => $user];
            $message="Facebook Login";
            $status_code=SUCCESSCODE;
        }

        return response([
            'data' => $data,
            'message' => $message,
            'status_code' => $status_code
        ], $status_code);
    }
    
}
