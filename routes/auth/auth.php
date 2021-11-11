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

    Route::post('reset_password', [AuthController::class, 'reset_password']);
    Route::post('forgot_password', [AuthController::class, 'forgot_password']);

     /*Artist api routes for login*/
     Route::post('artist/register', [AuthController::class, 'register']);
     Route::post('artist/login', [AuthController::class, 'login']);

     Route::post('artist/forgot_password', [AuthController::class, 'forgot_password']);
     Route::post('artist/reset_password', [AuthController::class, 'reset_password']);
 
     /*Admin api routes for login*/
     Route::post('admin/login', [AuthController::class, 'login']);
     Route::post('admin/register', [AuthController::class, 'register']);

     Route::post('admin/forgot_password', [AuthController::class, 'forgot_password']);
     Route::post('admin/reset_password', [AuthController::class, 'reset_password']);

    /* Seeder routes*/
    Route::get('role', [SeedController::class, 'role']);
    Route::get('genre_type', [SeedController::class, 'genre_type']);
    Route::get('post_type', [SeedController::class, 'post_type']);
    Route::get('subscription_type', [SeedController::class, 'subscription_type']);
    Route::get('when_to_post', [SeedController::class, 'when_to_post']);
    Route::get('who_can_see_post', [SeedController::class, 'who_can_see_post']);

});