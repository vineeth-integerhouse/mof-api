<?php

namespace App\Models;

use App\Notifications\Auth\ResetPasswordNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'email', 'password', 'username', 'name','google_id','facebook_id',
        'bio', 'role_id', 'profile_pic','payment_method','remember_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        //
    ];

    /* User to role relationship */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }


    public static function active_admin_list($user_role, $request)
    {
        $current_user = get_user();
  
        $limit = !empty($request->input('limit')) ? $request->input('limit') : 10;
        $sort_column = !empty($request->input('sort_column')) ? $request->input('sort_column') : "created_at";
        $sort_direction = !empty($request->input('sort_direction')) ? $request->input('sort_direction')  : "desc";
  
        $page = (!empty($request->input('page')) && $request->input('page') > 0) ? intval($request->input('page')) : 1;
        $offset = ($page > 1) ? ($limit * ($page - 1)) : 0;
  
        $users = User::select(
            'name',
            'email',
            'role_id',
            'role_name',
            DB::raw('users.id AS id'),
        )->leftJoin('roles', 'users.role_id', '=', 'roles.id')
           ->whereHas('role', function (Builder $query) use ($user_role) {
               if ($user_role) {
                   $query->where('role_name', $user_role);
               } else {
                   $query->where('role_name', '!=', 'User')->where('role_name', '!=', 'Artist');
               }
           })->where(DB::raw('users.id'), '!=', $current_user->id)
              ->where(
                  function ($query) use ($request) {
                      return $query
                      ->orWhere('name', 'like', "%{$request->search_string}%")
                      ->orWhere('email', 'like', "%{$request->search_string}%");
                  }
              )
                  ->orderBy(DB::raw('users.'.$sort_column), $sort_direction)->paginate($limit, $offset);
   
        return $users;
    }

    public static function active_user_list($user_role, $request)
    {
        $current_user = get_user();

        $limit = !empty($request->input('limit')) ? $request->input('limit') : 10;
        $sort_column = !empty($request->input('sort_column')) ? $request->input('sort_column') : "created_at";
        $sort_direction = !empty($request->input('sort_direction')) ? $request->input('sort_direction')  : "desc";

        $page = (!empty($request->input('page')) && $request->input('page') > 0) ? intval($request->input('page')) : 1;
        $offset = ($page > 1) ? ($limit * ($page - 1)) : 0;
 
        $users = User::select(
            'name',
            'username',
            'email',
            'id',
            'profile_pic',
        )->where('id', '!=', $current_user->id)
        ->where('role_id', $user_role)
        ->where(
            function ($query) use ($request) {
                return $query
                ->orWhere('name', 'like', "%{$request->search_string}%")
                ->orWhere('email', 'like', "%{$request->search_string}%");
            }
        )
            ->orderBy($sort_column, $sort_direction)->paginate($limit, $offset);
 
        return $users;
    }

    public static function active_artist_list($user_role, $request)
    {
        $current_user = get_user();

        $limit = !empty($request->input('limit')) ? $request->input('limit') : 10;
        $sort_column = !empty($request->input('sort_column')) ? $request->input('sort_column') : "created_at";
        $sort_direction = !empty($request->input('sort_direction')) ? $request->input('sort_direction')  : "desc";

        $page = (!empty($request->input('page')) && $request->input('page') > 0) ? intval($request->input('page')) : 1;
        $offset = ($page > 1) ? ($limit * ($page - 1)) : 0;
 
 
        $users = User::select(
            'name',
            'email',
            'username',
            'id',
            'profile_pic',
        )->where('id', '!=', $current_user->id)
        ->where('role_id', $user_role)
        ->where(
            function ($query) use ($request) {
                return $query
                ->orWhere('name', 'like', "%{$request->search_string}%")
                ->orWhere('email', 'like', "%{$request->search_string}%");
            }
        )
            ->orderBy($sort_column, $sort_direction)->paginate($limit, $offset);
 
        return $users;
    }

    public static function users_count($user_role)
    {
        $count  = User::with('role')
            ->whereHas('role', function (Builder $query) use ($user_role) {
                $query->select('id')->where('role_name', 'User');
            })->count();

        return $count;
    }

    public static function artists_count($user_role)
    {
        $count  = User::with('role')
            ->whereHas('role', function (Builder $query) use ($user_role) {
                $query->select('id')->where('role_name', 'Artist');
            })->count();

        return $count;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
