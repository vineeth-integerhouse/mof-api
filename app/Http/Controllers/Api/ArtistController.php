<?php


namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Genre;
use App\Models\Role;
use App\Models\SocialProfile;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;
use App\Rules\StrongPassword;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class ArtistController extends Controller
{
    /* Setup Profile */
    public function set_up_profile(Request $request, $artist_id)
    {
        $data = [];
      
        $message = '';
        $status_code = '';

        try {
            $update = [];
            if (isset($request->username)) {
                $update['username'] = $request->username;
            }
            if (isset($request->name)) {
                $update['name'] = $request->name;
            }
            if (isset($request->bio)) {
                $update['bio'] = $request->bio;
            }
            if (isset($request->website)) {
                $update['website'] = $request->website;
            }
            if (isset($request->merchandise_store)) {
                $update['merchandise_store'] = $request->merchandise_store;
            }
            if (isset($request->payment_method)) {
                $update['payment_method'] = $request->payment_method;
            }
    
            $update['profile_pic'] = $request->photo;

            $update['updated_at'] = date("Y-m-d H:i:s");

            if (count($update) != 0) {
                $role= Role::where('role_name', USER_ROLE_ARTIST)->first()->id;
                DB::table('users')->where('id', $artist_id)->where('role_id', $role)->update($update);
            }

            $genre= $request->genre_type_id;
            
            if (isset($genre)) {
                for ($i=0;$i<count($genre);$i++) {
                    $genre_data = ['user_id'=>$artist_id, 'genre_type_id' => $request->genre_type_id[$i]];
                    Genre::updateOrCreate($genre_data);
                }
            }
            
            $profile_update['social_profile_type_id'] = $request->social_profile_type_id;
            $profile_update['social_profile_username'] = $request->social_profile_username;
            $profile_update['user_id']=$artist_id;
           
            if (count($profile_update) != 0) {
                SocialProfile::updateOrCreate($profile_update);
            }
            
            $message = __('user.setup_success');
            $status_code = SUCCESSCODE;
        } catch (Exception $e) {
            $message = __('user.setup_failed') . ' ' . $e->getMessage();
            $status_code = BADREQUEST;
        }
       
        $data['User']= User::select(
            'id',
            'name',
            'username',
            'email',
            'profile_pic',
            'bio',
            'website',
            'merchandise_store',
            'payment_method',
        )->where('id', $artist_id)->get()->first();

        $data['Social Links']= SocialProfile::select(
            'id',
            'social_profile_type_id',
            'social_profile_username'
        )->where('user_id', $artist_id)->get();

        $data['Genre']= Genre::select(
            'id',
            'genre_type_id',
        )->where('user_id', $artist_id)->get();
    

        return response([
            'data'        => $data,
            'message'     => $message,
            'status_code' => $status_code
        ], $status_code);
    }

    public function social_profile_delete(Request $request, $artist_id, $social_id)
    {
        $user_data = [];
        $data      = [];
        $message =  __('user.invalid_user');
        $status_code = BADREQUEST;

        $user = SocialProfile::where('social_profile_type_id', $social_id)->where('user_id', $artist_id)->first();

        $user_data =  SocialProfile::where('social_profile_type_id', $social_id)->where('user_id', $artist_id)->delete();
       
        if ($user_data === 1) {
            $data['id']   = $user->id;
            $data['social_profile_type_id'] = $user->social_profile_type_id;
            $data['user_id'] = $user->user_id;
            $message = "Deleted social profile";
            $status_code = SUCCESSCODE;
        } else {
            $message = "Failed to delete social profile";
            $status_code = BADREQUEST;
        }
    
        return response([
            'data'        => $data,
            'message'     => $message,
            'status_code' => $status_code
        ], $status_code);
    }

    public function genre_delete(Request $request, $artist_id, $genre_id)
    {
        $user_data = [];
        $data      = [];
        $message =  __('user.invalid_user');
        $status_code = BADREQUEST;

        $user = Genre::where('genre_type_id', $genre_id)->where('user_id', $artist_id)->first();

        $user_data =  Genre::where('genre_type_id', $genre_id)->where('user_id', $artist_id)->delete();
       
        if ($user_data === 1) {
            $data['id']   = $user->id;
            $data['genre_type_id'] = $user->genre_type_id;
            $data['user_id'] = $user->user_id;
            $message = "Deleted genre";
            $status_code = SUCCESSCODE;
        } else {
            $message = "Failed to delete genre";
            $status_code = BADREQUEST;
        }
    
        return response([
            'data'        => $data,
            'message'     => $message,
            'status_code' => $status_code
        ], $status_code);
    }


    /* List Artist */
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

    /* Edit Artist */
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
                 
                $update['profile_pic'] = $request->profile_pic;
   
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
   

    /* Delete Artist From Admin */

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

    /* Update Password */

    public function update_password(Request $request)
    {
        $data = [];
        $error = [];
        $users_count = 0;
        $existing_password_check = true;
        $password_confirmation = true;
        $message = __('user.update_failed');
        $status_code = BADREQUEST;

        $user = User::find($request->id);

        if ($user) {
            try {
                $update = [];

                if (isset($request->password) && isset($request->password_confirmation) && isset($request->current_password)) {
                    if ($request->password === $request->password_confirmation) {
                        if (Hash::check($request->current_password, $user->password)) {
                            $update['password'] = bcrypt($request->password);
                        } else {
                            $existing_password_check = false;
                        }
                    } else {
                        $password_confirmation = false;
                    }
                    if (!Hash::check($request->current_password, $user->password)) {
                        $existing_password_check = false;
                    }
                }
        
                $update['updated_at'] = date("Y-m-d H:i:s");
                if (count($update) != 0) {
                    DB::table('users')->where('id', $user->id)->update($update);
                }
                $message = __('user.password');
                $status_code = SUCCESSCODE;

                $data= User::select(
                    'id',
                    'name',
                    'email',
                    'profile_pic'
                )->where('id', $user->id)->get()->first();
                
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

    /* Update Email */

    public function update_email(Request $request)
    {
        $data = [];
        $error = [];
        $users_count = 0;
        $existing_email_check = true;
        $email_confirmation = true;
        $message = __('user.update_failed');
        $status_code = BADREQUEST;

        $user = User::find($request->id);

        if ($user) {
            try {
                $update = [];

                if (isset($request->email) && isset($request->email_confirmation) && isset($request->current_email)) {
                    if ($request->email === $request->email_confirmation) {
                        if ($request->current_email==$user->email) {
                            $update['email'] = $request->email;
                        } else {
                            $existing_email_check = false;
                        }
                    } else {
                        $email_confirmation = false;
                    }
                    if ($request->current_email != $user->email) {
                        $existing_email_check = false;
                    }
                }

                if (isset($request->email)) {
                    $update['email'] = $request->email;
                    $users_count = User::withTrashed()->where('email', $request->email)->where('id', '!=', $request->id)->count();
                }
        
                $update['updated_at'] = date("Y-m-d H:i:s");
                if (count($update) != 0) {
                    DB::table('users')->where('id', $request->id)->update($update);
                }
                $message = __('user.email');
                $status_code = SUCCESSCODE;

                $data= User::select(
                    'id',
                    'name',
                    'email',
                    'profile_pic'
                )->where('id', $request->id)->get()->first();
            
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

    /* Delete Artist */

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

    /* Fetch Artist */

    public function fetch(Request $request, $artist_id)
    {
        $message =  __('user.not_artist');
        $status_code = BADREQUEST;
  
        $role= Role::where('role_name', USER_ROLE_ARTIST)->first()->id;

        $data= User::withTrashed()->select(
            'id',
            'email',
            'name',
            'username',
            'profile_pic',
            'deleted_at',
        )->where('id', $artist_id)->where('role_id', $role)->get()->first();

        ActivityLog::updateOrCreate(
            ['artist_id'=> $artist_id,
            'activity_type'=> "Impression"
           ],
            [
           'profile_impressions'=> DB::raw('profile_impressions+1'),
           ]
        );

        if (isset($data)) {
            $message = __('user.user_list_success');
            $status_code = SUCCESSCODE;
        }
  
        return response([
              'data'        => $data,
              'message'     => $message,
              'status_code' => $status_code
          ], $status_code);
    }

    //Add new artist
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
            $data['role_id'] = Role::where('role_name', USER_ROLE_ARTIST)->first()->id;
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

    /* fetch Artist */
    public function admin_fetch(Request $request, $artist_id)
    {
        $message =  __('user.not_artist');
        $status_code = BADREQUEST;
  
        $role= Role::where('role_name', USER_ROLE_ARTIST)->first()->id;

        $data= User::withTrashed()->select(
            'id',
            'email',
            'name',
            'username',
            'profile_pic',
            'bio',
            'website',
            'merchandise_store',
            'deleted_at',
        )->where('id', $artist_id)->where('role_id', $role)->first();

        $data['Social Links']= SocialProfile::select(
            'id',
            'social_profile_type_id',
            'social_profile_username'
        )->where('user_id', $artist_id)->get();

        $data['Genre']= Genre::select(
            'id',
            'genre_type_id',
        )->where('user_id', $artist_id)->get();
    

        if (isset($data)) {
            $message = __('user.user_list_success');
            $status_code = SUCCESSCODE;
        }
  
        return response([
              'data'        => $data,
              'message'     => $message,
              'status_code' => $status_code
          ], $status_code);
    }

    /* fetch Artist */
    public function tag(Request $request, $artist_id)
    {
        $message =  'Fialed to fetch tagged users';
        $status_code = BADREQUEST;

        $subscription = Subscription::select('id', 'user_id')
                            ->where('user_id', $artist_id)
                            ->get()->toArray();

        if (isset($subscription)) {
            foreach ($subscription as $type) {
                $user_subscription= UserSubscription::select('user_id')
                                    ->where('status', '1')
                                    ->where('subscribe_id', $type['id'])
                                    ->get();
            }
        }
 
        $users=[];
        if (isset($user_subscription)) {
            foreach ($user_subscription as $type) {
                array_push($users, User::select('id', 'name', 'profile_pic')
                     ->where('id', $type['user_id'])
                    ->first());
            }
        }

        if (isset($users)) {
            $message = 'Tagged Users';
            $status_code = SUCCESSCODE;
        }
   
        return response([
               'data'        => $users,
               'message'     => $message,
               'status_code' => $status_code
           ], $status_code);
    }

    public function settings(Request $request, $artist_id)
    {
        $message =  __('user.not_artist');
        $status_code = BADREQUEST;
  
        $role= Role::where('role_name', USER_ROLE_ARTIST)->first()->id;

        $data['User']= User::select(
            'id',
            'name',
            'username',
            'email',
            'profile_pic',
            'bio',
            'website',
            'merchandise_store',
            'payment_method',

        )->where('id', $artist_id)->where('role_id', $role)->get()->first();

        $data['Social Links']= SocialProfile::select(
            'id',
            'social_profile_type_id',
            'social_profile_username'
        )->where('user_id', $artist_id)->get();

        $data['Genre']= Genre::select(
            'id',
            'genre_type_id',
        )->where('user_id', $artist_id)->get();

        if (isset($data)) {
            $message = __('user.user_list_success');
            $status_code = SUCCESSCODE;
        }
  
        return response([
              'data'        => $data,
              'message'     => $message,
              'status_code' => $status_code
          ], $status_code);
    }

    public function user_artist(Request $request, $artist_id)
    {
        $message =  __('user.not_artist');
        $status_code = BADREQUEST;
  
        $role= Role::where('role_name', USER_ROLE_ARTIST)->first()->id;

        $data['User']= User::select(
                            'id',
                            'name',
                            'username',
                            'email',
                            'profile_pic',
                            'bio',
                            'website',
                            'merchandise_store',
                            'payment_method',
                            )->where('id', $artist_id)
                            ->where('role_id', $role)->first();

        $data['Social Links']= SocialProfile::select(
                                    'id',
                                    'social_profile_type_id',
                                    'social_profile_username'
                                    )->where('user_id', $artist_id)->get();

        $data['Genre']= Genre::select(
                            'id',
                            'genre_type_id',
                            )->where('user_id', $artist_id)->get();

        $data['Subscription']= Subscription::select(
                                    'subscriptions.id',
                                    'subscriptions.price',
                                    'subscription_types.subscription_type'
                                    ) ->leftJoin('subscription_types', 'subscription_types.id', '=', 'subscriptions.subscription_type_id')
                                    ->where('user_id', $artist_id)->first();

        if (isset($data)) {
            $message = __('user.user_list_success');
            $status_code = SUCCESSCODE;
        }
  
        return response([
              'data'        => $data,
              'message'     => $message,
              'status_code' => $status_code
          ], $status_code);
    }
}
