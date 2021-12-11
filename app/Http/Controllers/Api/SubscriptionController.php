<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Promotion;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    /* Add Subscription*/
    public function add(Request $request)
    {
        $data = [];
        $user_data = [];
        $message = __('user.subscription_failed');
        $status_code = BADREQUEST;

        $current_user = get_user();
        $inserted_data = Subscription::updateOrCreate(
            ['user_id' => $current_user->id
           ],
            [
                'subscription_type_id' => $request->subscription_type,
                'price' => $request->price,
            ]
        );

        $users= Subscription::select(
            'id',
            'subscription_type_id',
            'price',
            'user_id',
        )->where('id', $inserted_data->id)->get()->first();
        $message = __('user.subscription');
        $status_code = SUCCESSCODE;
        
        
        return response([
            'data' => $users,
            'message' => $message,
            'status_code' => $status_code,
        ], $status_code);
    }


      /* Add Promotion*/
    public function promotion_add(Request $request)
    {
        $data = [];
        $user_data = [];
        $message = __('user.promotion_failed');
        $status_code = BADREQUEST;

        $current_user = get_user();
        $promotion_data = Promotion::updateOrCreate(
            ['user_id' => $current_user->id
    ],
     [
         'starts_at' => $request->starts_at,
         'expires_at' => $request->expires_at,
         'price' => $request->price,
     ]);

        $users= Promotion::select(
            'id',
            'price',
            'user_id',
            'starts_at',
            'expires_at',
        )->where('id', $promotion_data->id)->get()->first();
        $message = __('user.promotion');
        $status_code = SUCCESSCODE;
        
        
        return response([
            'data' => $users,
            'message' => $message,
            'status_code' => $status_code,
        ], $status_code);
    }
    // create User Subscription
    public function user_subscription(Request $request)
    {
        $data = [];
        $user_data = [];
        $message = __('user.user_subscription_failed');
        $status_code = BADREQUEST;

        $current_user = get_user();
        $data['user_id'] = $current_user->id;
        $data['subscribe_id'] = $request->subscribe_id;
        $data['promotion_id'] = $request->promotion_id ;
        $data['status'] = $request->status;
     $user_data = UserSubscription::updateOrCreate($data);
        $users= UserSubscription::withTrashed()->select(
            'user_subscriptions.id',
            'user_subscriptions.user_id',
            'subscribe_id as subscription_id',
            'subscription_type',
            'subscriptions.price',
            'subscriptions.user_id as artist_id',
            'status',
            'promotion_id',
            'promotions.starts_at' ,
            'promotions.expires_at',
            
        )->leftJoin('subscriptions', 'subscriptions.id', '=', 'user_subscriptions.subscribe_id')
        ->leftJoin('promotions', 'promotions.id', '=', 'user_subscriptions.promotion_id')
        ->leftJoin('subscription_types', 'subscription_types.id', '=', 'subscriptions.subscription_type_id')
        ->where('user_subscriptions.id', $user_data->id)->get()->first();
        $message = __('user.user_subscription');
        $status_code = SUCCESSCODE;
        
        return response([
            'data' => $users,
            'message' => $message,
            'status_code' => $status_code,
        ], $status_code);
    }

}
