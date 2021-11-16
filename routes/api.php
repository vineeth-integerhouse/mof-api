<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ArtistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\TourController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\NotificationController;

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

        /********************** Setup account ************ */
        Route::put('setupaccount', [UserController::class, 'setupaccount']);

        /********************** Subscription************ */
        Route::post('subscription', [SubscriptionController::class, 'add']);
        /********************** Comment ************ */

        Route::post('comment', [CommentController::class, 'add']);
        Route::get('comment/{post_id}', [CommentController::class, 'fetch']);
        Route::delete('comment/{comment_id}', [CommentController::class, 'delete']);
        //  Route::get('artist/tour', [TourController::class, 'list']);


        Route::post('upload', [UploadController::class, 'upload']);

        /********************** Notification************ */
        Route::post('notification', [NotificationController::class, 'notification_settings']);
        Route::get('notification', [NotificationController::class, 'get']);
    });
});


Route::group([
    'middleware' => 'api'
], function () {
    Route::group([
        'middleware' => ['auth:api', 'artist']
    ], function () {
        Route::get('artist/logout', [AuthController::class, 'logout']);

        /********************** Settings ************ */

        Route::put('artist/password', [ArtistController::class, 'update_password']);
        Route::put('artist/email', [ArtistController::class, 'update_email']);

        /********************** Setup profile ************ */

        Route::put('artist/setupprofile', [ArtistController::class, 'setupprofile']);

        /********************** Delete account ************ */

        Route::delete('artist', [ArtistController::class, 'delete']);

        /********************** Post ************ */

        Route::post('artist/post', [PostController::class, 'add']);
        Route::put('artist/post/{post_id}', [PostController::class, 'update']);
        Route::delete('artist/post/{post_id}', [PostController::class, 'delete']);
        Route::get('artist/post/{post_id}', [PostController::class, 'get']);
        Route::get('artist/post', [PostController::class, 'list']);

        /********************** Tour ************ */

        Route::post('artist/tour', [TourController::class, 'add']);
        Route::put('artist/tour/{tour_id}', [TourController::class, 'update']);
        Route::get('artist/tour/{user_id}', [TourController::class, 'get']);
        Route::get('artist/tour', [TourController::class, 'list']);
        Route::delete('artist/tour/{tour_id}', [TourController::class, 'delete']);


        /*******************  Payments ****************** */
        Route::post('artist/payment', [PaymentController::class, 'stripe']);
        Route::get('artist/payment', [PaymentController::class, 'list']);

        /*******************  Subscription ****************** */
        Route::post('artist/subscription', [SubscriptionController::class, 'add']);
    });
});


Route::group([
    'middleware' => 'api'
], function () {
    Route::group([
        'middleware' => ['auth:api', 'superadmin']
    ], function () {
       
        /********************** Manage Admin ************ */
        Route::post('admin/admin', [AdminController::class, 'add']);
        Route::delete('admin/admin/{admin_id}', [AdminController::class, 'delete']);
        Route::put('admin/admin/{admin_id}', [AdminController::class, 'update']);
        Route::get('admin/admin', [AdminController::class, 'list']);

        /*********************** Statictics******************** */
        Route::get('admin/statistcs', [DashboardController::class, 'admin_statistcs']);
    });
});


Route::group([
    'middleware' => 'api'
], function () {
    Route::group([
        'middleware' => ['auth:api', 'admin']
    ], function () {
        Route::get('admin/logout', [AuthController::class, 'logout']);

        /********************** Manage User ************ */
        Route::get('admin/user', [UserController::class, 'list']);
        Route::delete('admin/user/{user_id}', [UserController::class, 'delete']);
        Route::put('admin/user/{user_id}', [UserController::class, 'update']);

        /********************** Manage Artist ************ */
        Route::get('admin/artist', [ArtistController::class, 'list']);
        Route::delete('admin/artist/{artist_id}', [ArtistController::class, 'admin_delete']);
        Route::put('admin/artist/{artist_id}', [ArtistController::class, 'update']);

        /********************** Settings ************ */
        Route::put('admin/settings', [AdminController::class, 'settings']);
    });
});

require __DIR__ . '/auth/auth.php';
