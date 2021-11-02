<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Subscription;

class SubscriptionController extends Controller
{
    /* Subscription*/
    public function add(Request $request)
    {
        $data = [];
        $user_data = [];
        $message = __('user.admin_add_failed');
        $status_code = BADREQUEST;


        $inserted_data = Subscription::updateOrCreate(
            ['user_id' => $request->user_id
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

}
