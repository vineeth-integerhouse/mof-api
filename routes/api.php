<?php

use App\Http\Controllers\Api\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'middleware' => 'api'
], function () {
    Route::group([
        'middleware' => 'auth:api'
    ], function () {
        Route::get('logout', [AuthController::class, 'logout']);
    });
});


Route::group([
    'middleware' => 'api'
], function () {
    Route::group([
        'middleware' => ['auth:api', 'superadmin']
    ], function () {
        Route::get('admin/logout', [AuthController::class, 'logout']);

        /********************** Manage Admin ************ */
        Route::post('admin/admin', [AdminController::class, 'add']);
        Route::delete('admin/admin/{admin_id}', [AdminController::class, 'delete']);
        Route::put('admin/admin/{admin_id}', [AdminController::class, 'update']);
        Route::get('admin/admin', [AdminController::class, 'list']);
    });
});


Route::group([
    'middleware' => 'api'
], function () {
    Route::group([
        'middleware' => ['auth:api', 'admin']
    ], function () {
        Route::get('admin/logout', [AuthController::class, 'logout']);

    });
});

require __DIR__ . '/auth/auth.php';
