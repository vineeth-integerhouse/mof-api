<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\SeedController;
use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => 'Api\Auth',
    'middleware' => 'api',
], function () {

    /*Customer api routes for login*/
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::post('reset_password', [AuthController::class, 'reset_password']);
    Route::post('forgot_password', [AuthController::class, 'forgot_password']);
   

    /* Seeder routes*/
    Route::get('role', [SeedController::class, 'role']);
});