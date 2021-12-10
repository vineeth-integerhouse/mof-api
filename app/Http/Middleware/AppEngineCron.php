<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AppEngineCron
{
    public function handle($request, Closure $next)
    {
        if (!$request->hasHeader('X-Appengine-Cron')) {
            return response()->json(trans('auth.unauthorized'), 401);
        }

        return $next($request);
    }
}
