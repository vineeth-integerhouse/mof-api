<?php

namespace App\Providers;

use App\Notifications\Auth\ResetPasswordNotification;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
         'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
        $this->registerPolicies();

        if (isset($request->role) && $request->role == 2) {
            ResetPasswordNotification::createUrlUsing(function ($user, string $token) {
                return config("auth.admin_reset_password_base_url") . $token . '&email=' . urlencode($user->email);
            });
        } else {
            ResetPasswordNotification::createUrlUsing(function ($user, string $token) {
                return config("auth.reset_password_base_url") . $token . '&email=' . urlencode($user->email);
            });
        }
    }
}
