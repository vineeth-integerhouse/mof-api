<?php


namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ArtistController extends Controller
{
    public function setupprofile(Request $request)
    {
        $data = [];
      
        $message = '';
        $status_code = '';

        $current_user=get_user();

        try {
            $update = [];
            if (isset($request->username)) {
                $update['username'] = $request->username;
            }
            if (isset($request->bio)) {
                $update['bio'] = $request->bio;
            }
    
             
            $update['profile_pic'] = $request->photo;

            $update['updated_at'] = date("Y-m-d H:i:s");

            if (count($update) != 0) {
                $role= Role::where('role_name', USER_ROLE_ARTIST)->first()->id;
                DB::table('users')->where('id', $current_user->id)->where('role_id', $role)->update($update);
            }
            $message = __('user.setup_success');
            $status_code = SUCCESSCODE;
        } catch (Exception $e) {
            $message = __('user.setup_failed') . ' ' . $e->getMessage();
            $status_code = BADREQUEST;
        }
       
        $data= User::select(
            'id',
            'name',
            'username',
            'email',
            'profile_pic',
            'bio'
        )->where('id', $current_user->id)->get()->first();
    

        return response([
            'data'        => $data,
            'message'     => $message,
            'status_code' => $status_code
        ], $status_code);
    }


    /*Artist Listing*/
    public function list(Request $request)
    {
        $data = [];
        $message =  __('user.user_list_failed');
        $status_code = BADREQUEST;
    
        $role= Role::where('role_name', USER_ROLE_ARTIST)->first()->id;
        $data = User::active_artist_list($role, $request);
        $message = __('user.user_list_success');
        $status_code = SUCCESSCODE;
   
        return response([
               'data'        => $data,
               'message'     => $message,
               'status_code' => $status_code
           ], $status_code);
    }

    /*Artist Edit*/
    public function update(Request $request, $artist_id)
    {
        $data = [];
        $message     =  '';
        $status_code = '';
     
        $user_data = User::find($artist_id);
              
        if ($user_data) {
            try {
                $update = [];
                if (isset($request->name)) {
                    $update['name'] = $request->name;
                }
                if (isset($request->username)) {
                    $update['username'] = $request->username;
                }
                if (isset($request->email)) {
                    $update['email'] = $request->email;
                }
                 
                $update['profile_pic'] = $request->photo;
   
                $update['updated_at'] = date("Y-m-d H:i:s");
   
                if (count($update) != 0) {
                    $role= Role::where('role_name', USER_ROLE_ARTIST)->first()->id;
                    DB::table('users')->where('id', $artist_id)->where('role_id', $role)->update($update);
                }
                $message = __('user.update_success');
                $status_code = SUCCESSCODE;
            } catch (Exception $e) {
                $message = __('user.update_failed') . ' ' . $e->getMessage();
                $status_code = BADREQUEST;
            }
           
            $data= User::select(
                'id',
                'name',
                'username',
                'email',
                'profile_pic'
            )->where('id', $artist_id)->get()->first();
        }
   
        return response([
                'data'        => $data,
                'message'     => $message,
                'status_code' => $status_code
            ], $status_code);
    }
   

    /*Artist Delete From Admin*/

    public function admin_delete(Request $request, $artist_id)
    {
        $user_data = [];
        $data      = [];
        $message =  __('user.invalid_user');
        $status_code = BADREQUEST;

        $role= Role::where('role_name', USER_ROLE_ARTIST)->first()->id;

        $user = User::where('id', $artist_id)->first();

        $user_data = User::where('id', $artist_id)->where('role_id', $role)->delete();
       
        if ($user_data === 1) {
            $data['id']   = $user->id;
            $data['email'] = $user->email;
            $message = __('user.artist_delete_success');
            $status_code = SUCCESSCODE;
        } else {
            $message = __('user.not_artist');
            $status_code = BADREQUEST;
        }
    
        return response([
            'data'        => $data,
            'message'     => $message,
            'status_code' => $status_code
        ], $status_code);
    }



    /* Update password */

    public function update_password(Request $request)
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

                if (isset($request->password) && isset($request->password_confirmation) && isset($request->current_password)) {
                    if ($request->password === $request->password_confirmation) {
                        if (Hash::check($request->current_password, $current_user->password)) {
                            $update['password'] = bcrypt($request->password);
                        } else {
                            $existing_password_check = false;
                        }
                    } else {
                        $password_confirmation = false;
                    }
                    if (!Hash::check($request->current_password, $current_user->password)) {
                        $existing_password_check = false;
                    }
                }
        
                $update['updated_at'] = date("Y-m-d H:i:s");
                if (count($update) != 0) {
                    DB::table('users')->where('id', $current_user->id)->update($update);
                }
                $message = __('user.password');
                $status_code = SUCCESSCODE;

                $data= User::select(
                    'id',
                    'name',
                    'email',
                    'profile_pic'
                )->where('id', $current_user->id)->get()->first();
                
                $error = [];
                if ($password_confirmation == false) {
                    $data=[];
                    array_push($error, array('type'=>'password_confirmation','message'=> __('user.password_not_match')));
                    $status_code = BADREQUEST;
                }
        
                if ($existing_password_check == false) {
                    $data=[];
                    array_push($error, array('type'=>'current_password','message'=> __('user.wrong_password')));
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

    public function update_email(Request $request)
    {
        $data = [];
        $error = [];
        $users_count = 0;
        $existing_email_check = true;
        $email_confirmation = true;
        $message = __('user.update_failed');
        $status_code = BADREQUEST;

        $current_user=get_user();

        if ($current_user) {
            try {
                $update = [];

                if (isset($request->email) && isset($request->email_confirmation) && isset($request->current_email)) {
                    if ($request->email === $request->email_confirmation) {
                        if ($request->current_email==$current_user->email) {
                            $update['email'] = $request->email;
                        } else {
                            $existing_email_check = false;
                        }
                    } else {
                        $email_confirmation = false;
                    }
                    if ($request->current_email != $current_user->email) {
                        $existing_email_check = false;
                    }
                }

                if (isset($request->email)) {
                    $update['email'] = $request->email;
                    $users_count = User::withTrashed()->where('email', $request->email)->where('id', '!=', $current_user->id)->count();
                }
        
                $update['updated_at'] = date("Y-m-d H:i:s");
                if (count($update) != 0) {
                    DB::table('users')->where('id', $current_user->id)->update($update);
                }
                $message = __('user.email');
                $status_code = SUCCESSCODE;

                $data= User::select(
                    'id',
                    'name',
                    'email',
                    'profile_pic'
                )->where('id', $current_user->id)->get()->first();
            
                $error = [];

                if ($users_count!=0) {
                    $data=[];
              
                    array_push($error, array('type'=>'email','message'=> __('user.email_in_use')));
                    $status_code = BADREQUEST;
                }
                if ($email_confirmation == false) {
                    $data=[];
                    array_push($error, array('type'=>'email_confirmation','message'=> __('user.email_not_match')));
                    $status_code = BADREQUEST;
                }
        
                if ($existing_email_check == false) {
                    $data=[];
                    array_push($error, array('type'=>'current_email','message'=> __('user.wrong_email')));
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

    /*Artist Delete*/

    public function delete(Request $request)
    {
        $user_data = [];
        $data      = [];
        $message =  __('user.invalid_user');
        $status_code = BADREQUEST;

        $current_user=get_user();
 
        $role= Role::where('role_name', USER_ROLE_ARTIST)->first()->id;

        $user_data = User::where('id', $current_user->id)->where('role_id', $role)->delete();
        
        if ($user_data === 1) {
            $data['id']   = $current_user->id;
            $data['email'] = $current_user->email;
            $message = __('user.artist_delete_success');
            $status_code = SUCCESSCODE;
        } else {
            $message = __('user.not_artist');
            $status_code = BADREQUEST;
        }
     
        return response([
             'data'        => $data,
             'message'     => $message,
             'status_code' => $status_code
         ], $status_code);
    }
}