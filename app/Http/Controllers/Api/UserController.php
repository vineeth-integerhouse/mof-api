<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Role;
use App\Models\User;
use App\Rules\StrongPassword;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{

     //Add new user
     public function add(Request $request)
     {
         $data = [];
         $users=[];
         $message = __('user.user_add_failed');
         $status_code = BADREQUEST;
 
         $validate_data = Validator::make($request->all(), [
             'email' => 'email|required|unique:users',
             'password' => [new StrongPassword],
         ]);
 
         if ($validate_data->fails()) {
             $errors = $validate_data->errors();
             $message =  implode(', ', $errors->all());
         } else {
             $data['role_id'] = Role::where('role_name', USER_ROLE_USER)->first()->id;
             $data['name'] = $request->name;
             $data['username'] = $request->name;
             $data['email'] = $request->email;
             $data['password'] = bcrypt($request->password);
           
             $inserted_data = User::create($data);
 
             $users= User::select(
                 'id',
                 'role_id',
                 'name',
                 'username',
                 'email',
             )->where('id', $inserted_data->id)->get()->first();
             $message = __('user.user_add_success');
             $status_code = SUCCESSCODE;
             try {
                 $details = [
                    'email' => $request->email,
                    'password' => $request->password,
             ];
                 Mail::to($request->email)->send(new \App\Mail\WelcomeMail($details));
             } catch (\Exception $e) {
                 $message = "Invalid email given for new user";
             }
         }
         
         return response([
             'data' => $users,
             'message' => $message,
             'status_code' => $status_code,
         ], $status_code);
     }
     
    /*Account Setup*/

    public function set_up_account(Request $request)
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
             
            $update['profile_pic'] = $request->photo;

            $update['updated_at'] = date("Y-m-d H:i:s");

            if (count($update) != 0) {
                $role= Role::where('role_name', USER_ROLE_USER)->first()->id;
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
        )->where('id', $current_user->id)->get()->first();
    

        return response([
            'data'        => $data,
            'message'     => $message,
            'status_code' => $status_code
        ], $status_code);
    }

    /* List User */
    public function list(Request $request)
    {
        $data = [];
        $message =  __('user.user_list_failed');
        $status_code = BADREQUEST;
    
        $role= Role::where('role_name', USER_ROLE_USER)->first()->id;
        $data = User::active_user_list($role, $request);
        $message = __('user.user_list_success');
        $status_code = SUCCESSCODE;
   
        return response([
               'data'        => $data,
               'message'     => $message,
               'status_code' => $status_code
           ], $status_code);
    }

    /* Edit User */
    public function update(Request $request, $user_id)
    {
        $data = [];
        $message     =  '';
        $status_code = '';
     
        $user_data = User::find($user_id);
              
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
                 
                $update['profile_pic'] = $request->profile_pic;
   
                $update['updated_at'] = date("Y-m-d H:i:s");
   
                if (count($update) != 0) {
                    $role= Role::where('role_name', USER_ROLE_USER)->first()->id;
                    DB::table('users')->where('id', $user_id)->where('role_id', $role)->update($update);
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
            )->where('id', $user_id)->get()->first();
        }
   
        return response([
                'data'        => $data,
                'message'     => $message,
                'status_code' => $status_code
            ], $status_code);
    }
   

    /* Delete User */

    public function delete(Request $request, $user_id)
    {
        $user_data = [];
        $data      = [];
        $message =  __('user.invalid_user');
        $status_code = BADREQUEST;

        $role= Role::where('role_name', USER_ROLE_USER)->first()->id;

        $user = User::where('id', $user_id)->first();

        $user_data = User::where('id', $user_id)->where('role_id', $role)->delete();
       
        if ($user_data === 1) {
            $data['id']   = $user->id;
            $data['email'] = $user->email;
            $message = __('user.user_delete_success');
            $status_code = SUCCESSCODE;
        } else {
            $message = __('user.not_user');
            $status_code = BADREQUEST;
        }
    
        return response([
            'data'        => $data,
            'message'     => $message,
            'status_code' => $status_code
        ], $status_code);
    }

    /* fetch User */
    public function fetchOne(Request $request, $user_id)
    {
        $users = [];
        $message =  __('user.user_list_failed');
        $status_code = BADREQUEST;
  
        $users = User::select(
            'id',
            'email',
            'name',
            'role_id'
        )->with('role', function ($query) {
            $query->select('id', 'role_name');
        })->where('id', $user_id)->get();
        
        $message = __('user.user_list_success');
        $status_code = SUCCESSCODE;
 
        return response([
             'data'        => $users,
             'message'     => $message,
             'status_code' => $status_code
         ], $status_code);
    }

}
