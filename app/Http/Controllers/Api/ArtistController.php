<?php


namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class ArtistController extends Controller
{
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
   

    /*Artist Delete*/

    public function delete(Request $request, $artist_id)
    {
        $user_data = [];
        $data      = [];
        $message =  __('user.invalid_user');
        $status_code = BADREQUEST;

        $role= Role::where('role_name', USER_ROLE_ARTIST)->first()->id;

        $user_data = User::where('id', $artist_id)->where('role_id', $role)->delete();
       
        if ($user_data === 1) {
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
