<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdmin
{
    /**
     * Handle an incoming request.
     * 
     * checking the every admin routes has an authorized access or not.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /*Checking the role has exist or not*/
        if (!isset(Auth::guard('api')->user()->role->role_name)) {
            return response([
                'message'     => __('auth.unauthorized'),
                'status'      => false,
                'status_code' => BADREQUEST
            ]);
        } else {
            $user_role = Auth::guard('api')->user()->role->role_name;
           
            if ($user_role !== USER_ROLE_SUPERADMIN) {
                return response([
                    'message'     => __('auth.unauthorized'),
                    'status'      => false,
                    'status_code' => BADREQUEST
                ]);
            } 
        }
        return $next($request);
    }
}
