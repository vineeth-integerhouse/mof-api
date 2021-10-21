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
   

    /* Seeder routes*/
    Route::get('role', [SeedController::class, 'role']);
});