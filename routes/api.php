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
use App\Http\Controllers\Api\SeedController;

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
      
        Route::put('user', [UserController::class, 'set_up_account']);

        Route::get('user/artists', [UserController::class, 'artist_list']);
        Route::get('user/artist/{artist_id}', [ArtistController::class, 'settings']);

        Route::get('user/artist/{artist_id}/posts', [PostController::class, 'artist_publish_list']);

        Route::get('user/artist/{artist_id}/tours', [TourController::class, 'artist_tours']);

        /********************** Comment ************ */
        Route::post('comment', [CommentController::class, 'add']);
        Route::get('post/{post_id}/comment', [CommentController::class, 'fetch']);
        Route::delete('comment/{comment_id}', [CommentController::class, 'delete']);


        /********************** Like ************ */
        Route::post('like', [CommentController::class, 'add_like']);

          /********************** Profile View ************ */
          Route::post('profile_view', [CommentController::class, 'profile_views']);

        /***************** Payment ************* */
        Route::post('payment', [PaymentController::class, 'stripe']);
        Route::put('payment', [PaymentController::class, 'update']);

        /***************** Upload ************* */
        Route::post('upload', [UploadController::class, 'upload']);

        // Route::get('user_feed', [PostController::class, 'feed']);
         
        /********************** Notification************ */
        Route::post('notification', [NotificationController::class, 'notification_settings']);
        Route::get('notification', [NotificationController::class, 'get']);

        /********************* subscription ************** */
        Route::post('subscribe', [SubscriptionController::class, 'user_subscription']);
        Route::put('unsubscribe/{subscribe_id}', [SubscriptionController::class, 'unsubscribe']);
        Route::get('user/{user_id}/subscribe', [SubscriptionController::class, 'list']);

        Route::get('user/{user_id}/settings', [UserController::class, 'settings']);
        Route::put('user_password', [ArtistController::class, 'update_password']);
        Route::put('user_email', [ArtistController::class, 'update_email']);


        // // /********************** fetch profile ************ */

        //  Route::get('artist/{artist_id}', [ArtistController::class, 'fetch']);

        /* Seeder routes*/
        Route::get('role', [SeedController::class, 'role']);
        Route::get('genre_type', [SeedController::class, 'genre_type']);
        Route::get('post_type', [SeedController::class, 'post_type']);
        Route::get('subscription_type', [SeedController::class, 'subscription_type']);
        Route::get('when_to_post', [SeedController::class, 'when_to_post']);
        Route::get('who_can_see_post', [SeedController::class, 'who_can_see_post']);
        Route::get('profile_type', [SeedController::class, 'profile_type']);
        Route::get('notification_type', [SeedController::class, 'notification_type']);
    });
});


Route::group([
    'middleware' => 'api'
], function () {
    Route::group([
        'middleware' => ['auth:api', 'artist']
    ], function () {

        Route::get('artist/scheduled_post', [PostController::class, 'scheduled_list']);
        Route::get('artist/saved_post', [PostController::class, 'saved_list']);

        Route::get('artist/publish_post', [PostController::class, 'publish_list']);
       

        Route::get('artist_tag/{artist_id}  ', [ArtistController::class, 'tag']);
        
        Route::get('artist/{artist_id}', [ArtistController::class, 'fetch']);

    
        Route::get('artist/logout', [AuthController::class, 'logout']);

        /********************** Setup profile ************ */

        Route::put('artist/{artist_id}', [ArtistController::class, 'set_up_profile']);

        Route::delete('artist/{artist_id}/soial_profile/{social_id}', [ArtistController::class, 'social_profile_delete']);

        Route::delete('artist/{artist_id}/genre/{genre_id}', [ArtistController::class, 'genre_delete']);

        /********************** Settings ************ */

        Route::get('artist/{artist_id}/settings', [ArtistController::class, 'settings']);
        Route::put('artist_password', [ArtistController::class, 'update_password']);
        Route::put('artist_email', [ArtistController::class, 'update_email']);


        /********************** Delete account ************ */

        Route::delete('artist', [ArtistController::class, 'delete']);

        /********************** Post ************ */

        Route::post('artist/post', [PostController::class, 'add']);
        Route::put('artist/post/{post_id}', [PostController::class, 'update']);
        Route::delete('artist/post/{post_id}', [PostController::class, 'delete']);
       
        Route::get('artist/post/{post_id}', [PostController::class, 'get']);

        /********************** Tour ************ */

        Route::post('artist/tour', [TourController::class, 'add']);
        Route::put('artist/tour/{tour_id}', [TourController::class, 'update']);
        Route::get('artist/{artist_id}/tour', [TourController::class, 'get']);
        Route::get('artist_tour', [TourController::class, 'list']);
        Route::delete('artist/tour/{tour_id}', [TourController::class, 'delete']);


        /*******************  Payments ****************** */
        Route::get('artist_payment', [PaymentController::class, 'artist_payment_list']);
        Route::get('artist_payout', [PaymentController::class, 'artist_payout_list']);

        /*******************  Subscription ****************** */
        Route::post('artist/subscription', [SubscriptionController::class, 'add']);
        // Route::delete('artist/subscription/{subscription_id}', [SubscriptionController::class, 'delete']);


        Route::post('artist/promotion', [SubscriptionController::class, 'promotion_add']);
        /*******************  Dashboard  ****************** */

        Route::get('artist_dashboard', [DashboardController::class, 'dashboard_statistcs']);
     
        Route::get('artist_dashboard/fans', [DashboardController::class, 'fans']);
        Route::get('artist_dashboard/earnings', [DashboardController::class, 'earnings']);

        Route::get('artist_dashboard/views', [DashboardController::class, 'profile_views']);
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
        Route::post('admin/user', [UserController::class, 'add']);
        Route::get('admin/user', [UserController::class, 'list']);
        Route::get('admin/user/{user_id}', [UserController::class, 'admin_fetch']);
        Route::delete('admin/user/{user_id}', [UserController::class, 'delete']);
        Route::put('admin/user/{user_id}', [UserController::class, 'update']);

        /********************** Manage Artist ************ */
        Route::post('admin/artist', [ArtistController::class, 'add']);
        Route::get('admin/artist', [ArtistController::class, 'list']);
        Route::get('admin/artist/{artist_id}', [ArtistController::class, 'admin_fetch']);
        Route::delete('admin/artist/{artist_id}', [ArtistController::class, 'admin_delete']);
        Route::put('admin/artist/{artist_id}', [ArtistController::class, 'update']);

        Route::put('admin/artist/{artist_id}', [ArtistController::class, 'set_up_profile']);

        Route::delete('admin/artist/{artist_id}/soial_profile/{social_id}', [ArtistController::class, 'social_profile_delete']);

        Route::delete('admin/artist/{artist_id}/genre/{genre_id}', [ArtistController::class, 'genre_delete']);

        Route::post('admin/artist/{artist_id}/tour', [TourController::class, 'admin_add']);
        Route::get('admin/artist/{artist_id}/tour', [TourController::class, 'get']);
        Route::put('admin/artist/{artist_id}/tour/{tour_id}', [TourController::class, 'admin_update']);
        Route::delete('admin/artist/{artist_id}/tour/{tour_id}', [TourController::class, 'admin_delete']);

        Route::get('admin/artist/{artist_id}/subscription', [SubscriptionController::class, 'get']);
        Route::put('admin/artist/{artist_id}/subscription/{subscription_id}', [SubscriptionController::class, 'update']);

        Route::get('admin/artist/{artist_id}/promotion', [SubscriptionController::class, 'get_promotion']);
        Route::put('admin/artist/{artist_id}/promotion/{promotion_id}', [SubscriptionController::class, 'update_promotion']);

        /********************** Settings ************ */
        Route::put('admin/settings', [AdminController::class, 'settings']);
        Route::get('admin/{admin_id}/settings', [AdminController::class, 'fetch_settings']);

        /********************* Payout ************** */
        Route::get('admin/payment', [PaymentController::class, 'payment_list']);
        Route::get('admin/payment/{payment_id}', [PaymentController::class, 'payment_fetch']);
        Route::get('admin/payout', [PaymentController::class, 'payout_list']);
        Route::get('admin/payout/{payout_id}', [PaymentController::class, 'payout_fetch']);

        /********************* subscription ************** */
        Route::get('admin/subscription', [SubscriptionController::class, 'admin_list']);
         Route::get('admin/user/{user_id}/subscription/{subscription_id}', [SubscriptionController::class, 'admin_fetch']);

        // Route::get('admin/subscription/{subscription_id}', [SubscriptionController::class, 'admin_fetch']);

      Route::get('admin/user/{user_id}/subscription', [SubscriptionController::class, 'admin_usersubscritpion_fetch']);
    });
});

Route::group([
    'middleware' => 'api'
], function () {
    Route::group([
        'middleware' => ['auth:api', 'superadmin']
    ], function () {

        /*********************** Statictics******************** */
        Route::get('admin/statistcs', [DashboardController::class, 'admin_statistcs']);
       
        /********************** Manage Admin ************ */
        Route::post('admin', [AdminController::class, 'add']);
        Route::delete('admin/{admin_id}', [AdminController::class, 'delete']);
        Route::put('admin/{admin_id}', [AdminController::class, 'update']);
        Route::get('admin', [AdminController::class, 'list']);
        Route::get('admin/{admin_id}', [AdminController::class, 'fetch']);
    });
});


require __DIR__ . '/auth/auth.php';
