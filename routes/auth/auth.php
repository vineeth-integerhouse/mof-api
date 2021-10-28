<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\SeedController;
use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => 'Api\Auth',
    'middleware' => 'api',
], function () {

    /*Users api routes for login*/
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

     /*Artist api routes for login*/
     Route::post('artist/register', [AuthController::class, 'register']);
     Route::post('artist/login', [AuthController::class, 'login']);
 
     /*Admin api routes for login*/
     Route::post('admin/login', [AuthController::class, 'login']);
     Route::post('admin/register', [AuthController::class, 'register']);

    Route::post('reset_password', [AuthController::class, 'reset_password']);
    Route::post('forgot_password', [AuthController::class, 'forgot_password']);
   

    /* Seeder routes*/
    Route::get('role', [SeedController::class, 'role']);
});