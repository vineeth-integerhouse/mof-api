<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Promotion;
use App\Models\UserSubscription;
use Exception;
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


    public function delete(Request $request, $subscription_id)
    {
        $user_data = [];
        $data      = [];
        $message =  __('user.invalid_user');
        $status_code = BADREQUEST;

        $current_user = get_user();

        $subscription = Subscription::where('id', $subscription_id)->first();

        $subscription_data = Subscription::where('id', $subscription_id)->where('user_id', $current_user->id)->delete();
       
        if ($subscription_data === 1) {
            $data['id']   = $subscription->id;
            $message = __('user.subscription_delete_success');
            $status_code = SUCCESSCODE;
        } else {
            $message = __('user.subscription_delete_failed');
            $status_code = BADREQUEST;
        }
    
        return response([
            'data'        => $data,
            'message'     => $message,
            'status_code' => $status_code
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
                                ]
                            );

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

        $user_data=  UserSubscription::updateOrCreate(
            ['subscribe_id' => $request->subscribe_id,
       ],
            [
            'user_id' => $current_user->id,
            'promotion_id' => $request->promotion_id,
            'status'=> $request->status
        ]
        );
        $users= UserSubscription::select(
            'user_subscriptions.id',
            'user_subscriptions.user_id',
            'subscribe_id as subscription_id',
            'subscription_type',
            'subscriptions.price',
            'subscriptions.user_id as artist_id',
            'status',
            'promotion_id',
            'promotions.starts_at',
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


    // Lisitng users subscription

    public function list(Request $request)
    {
        $data = [];
        $message = __('user.user_subscription_failed');
        $status_code = BADREQUEST;

        $current_user = get_user();
        $limit = !empty($request->input('limit')) ? $request->input('limit') : 10;
        $sort_column = !empty($request->input('sort_column')) ? $request->input('sort_column') : "created_at";
        $sort_direction = !empty($request->input('sort_direction')) ? $request->input('sort_direction')  : "desc";
 
        $page = (!empty($request->input('page')) && $request->input('page') > 0) ? intval($request->input('page')) : 1;
        $offset = ($page > 1) ? ($limit * ($page - 1)) : 0;

        $users= UserSubscription::select(
            'user_subscriptions.id',
            'user_subscriptions.user_id',
            'subscribe_id as subscription_id',
            'subscription_type as subscription',
            'subscriptions.price',
            'subscriptions.user_id as artist_id',
            'users.name',
            'users.username',
            'users.profile_pic',
            'promotion_id',
            'promotions.starts_at',
            'promotions.expires_at',
        )->leftJoin('subscriptions', 'subscriptions.id', '=', 'user_subscriptions.subscribe_id')
                    ->leftJoin('promotions', 'promotions.id', '=', 'user_subscriptions.promotion_id')
                    ->leftJoin('subscription_types', 'subscription_types.id', '=', 'subscriptions.subscription_type_id')
                    ->leftJoin('users', 'users.id', '=', 'subscriptions.user_id')
                    ->where('user_subscriptions.user_id', $current_user->id)
                    ->where('user_subscriptions.deleted_at', null)
                    ->where('user_subscriptions.status', 1)
                    ->orderBy(DB::raw('user_subscriptions.'.$sort_column), $sort_direction)->paginate($limit, $offset);
        $message = __('user.user_subscription');
        $status_code = SUCCESSCODE;
        
        return response([
            'data' => $users,
            'message' => $message,
            'status_code' => $status_code,
        ], $status_code);
    }

    public function unsubscribe(Request $request, $subscription_id)
    {
        $data = [];
        $message     =  '';
        $status_code = '';
  
        $subscribe = UserSubscription::find($subscription_id);

        $current_user = get_user();
     
        if ($subscribe) {
            try {
                $update = [];

                $update['status'] = $request->status;
                DB::table('user_subscriptions')->where('id', $subscription_id)->update($update);
                $data= DB::table('user_subscriptions')
                            ->select(
                                'user_subscriptions.id as user_subscription_id',
                                'user_subscriptions.user_id as user',
                                'status',
                                'subscriptions.user_id as artist_id',
                                'users.name as artist_name',
                                'users.username as artist_username',
                                'subscription_types.subscription_type as subscription',
                            )->leftJoin('subscriptions', 'subscriptions.id', '=', 'user_subscriptions.subscribe_id')
                            ->leftJoin('users', 'users.id', '=', 'subscriptions.user_id')
                            ->leftJoin('subscription_types', 'subscription_types.id', '=', 'subscriptions.subscription_type_id')
                            ->where('user_subscriptions.id', $subscription_id)
                            ->where('user_subscriptions.user_id', $current_user->id)->get()->first();
                $message = __('user.unsubscribe_success');
                $status_code = SUCCESSCODE;
            } catch (Exception $e) {
                $data=[];
                $message = __('user.unsubscribe_failed') . ' ' . $e->getMessage();
                $status_code = BADREQUEST;
            }
        }

        return response([
             'data'        => $data,
             'message'     => $message,
             'status_code' => $status_code
         ], $status_code);
    }


    //admin fetch active subscriptions
    public function admin_list(Request $request)
    {
        $data = [];
        $message = __('user.admin_subscription_failed');
        $status_code = BADREQUEST;

        $current_user = get_user();
        $limit = !empty($request->input('limit')) ? $request->input('limit') : 10;
        $sort_column = !empty($request->input('sort_column')) ? $request->input('sort_column') : "created_at";
        $sort_direction = !empty($request->input('sort_direction')) ? $request->input('sort_direction')  : "desc";
 
        $page = (!empty($request->input('page')) && $request->input('page') > 0) ? intval($request->input('page')) : 1;
        $offset = ($page > 1) ? ($limit * ($page - 1)) : 0;

        $users= UserSubscription::select(
            'user_subscriptions.id',
            'user_subscriptions.user_id',
            'users.name',
            'users.username as username',
            'users.profile_pic',
            'subscribe_id as subscribe_id',
            'subscription_type as subscription',
            'subscriptions.price',
            'subscriptions.user_id as artist_id',
            'status',
            'promotion_id',
            'promotions.starts_at',
            'promotions.expires_at',
        )->leftJoin('subscriptions', 'subscriptions.id', '=', 'user_subscriptions.subscribe_id')
                            ->leftJoin('promotions', 'promotions.id', '=', 'user_subscriptions.promotion_id')
                            ->leftJoin('subscription_types', 'subscription_types.id', '=', 'subscriptions.subscription_type_id')
                            ->leftJoin('users', 'users.id', '=', 'user_subscriptions.user_id')
                            ->where('subscriptions.deleted_at', null)
                            ->where('user_subscriptions.status', 1)
                            ->orderBy(DB::raw('user_subscriptions.'.$sort_column), $sort_direction)->paginate($limit, $offset);
            
        $message = __('user.admin_subscription');
        $status_code = SUCCESSCODE;
        
        return response([
            'data' => $users,
            'message' => $message,
            'status_code' => $status_code,
        ], $status_code);
    }

     
    public function admin_fetch(Request $request, $user_id, $subscription_id)
    {
        $data = [];
        $message = __('user.admin_subscription_failed');
        $status_code = BADREQUEST;

        $users= UserSubscription::select(
                    'user_subscriptions.id',
                    'user_subscriptions.user_id',
                    'subscribe_id as subscription_id',
                    'subscription_type as subscription',
                    'subscriptions.price',
                    'subscriptions.user_id as artist_id',
                    'users.name',
                    'users.profile_pic',
                    'users.username',
                    'promotion_id',
                    'promotions.starts_at',
                    'promotions.expires_at',
                     )->leftJoin('subscriptions', 'subscriptions.id', '=', 'user_subscriptions.subscribe_id')
                    ->leftJoin('promotions', 'promotions.id', '=', 'user_subscriptions.promotion_id')
                    ->leftJoin('subscription_types', 'subscription_types.id', '=', 'subscriptions.subscription_type_id')
                    ->leftJoin('users', 'users.id', '=', 'subscriptions.user_id')
                    ->where('user_subscriptions.deleted_at', null)
                    ->where('user_subscriptions.id', $subscription_id)->get()->first();

                
        $message = __('user.admin_subscription');
        $status_code = SUCCESSCODE;
        
        return response([
            'data' => $users,
            'message' => $message,
            'status_code' => $status_code,
        ], $status_code);
    }

    public function admin_usersubscritpion_fetch(Request $request, $user_id)
    {
        $data = [];
        $message = __('user.admin_subscription_failed');
        $status_code = BADREQUEST;

        $users= UserSubscription::select(
            'user_subscriptions.id',
            'user_subscriptions.user_id',
            'subscribe_id as subscription_id',
            'subscription_type as subscription',
            'subscriptions.price',
            'subscriptions.user_id as artist_id',
            'user_subscriptions.status',
            'users.name',
            'users.profile_pic',
            'users.username',
            'promotion_id',
            'promotions.starts_at',
            'promotions.expires_at',
        )->leftJoin('subscriptions', 'subscriptions.id', '=', 'user_subscriptions.subscribe_id')
                        ->leftJoin('promotions', 'promotions.id', '=', 'user_subscriptions.promotion_id')
                        ->leftJoin('subscription_types', 'subscription_types.id', '=', 'subscriptions.subscription_type_id')
                        ->leftJoin('users', 'users.id', '=', 'subscriptions.user_id')
                        ->where('user_subscriptions.deleted_at', null)
                        ->where('user_subscriptions.status', 1)
                        ->where('user_subscriptions.user_id', $user_id)->get();

        $message = __('user.admin_subscription');
        $status_code = SUCCESSCODE;
        
        return response([
            'data' => $users,
            'message' => $message,
            'status_code' => $status_code,
        ], $status_code);
    }

    /* Fetch Subscription*/
    public function get(Request $request, $artist_id)
    {
        $message =  "Failed to fetch artist subscription";
        $status_code = BADREQUEST;
     
        $data= DB::table('subscriptions')
                    ->select(
                        'id',
                        'subscription_type_id',
                        'price',
                        'user_id',
                    )->where('user_id', $artist_id)->first();
     
        if (isset($data)) {
            $message = "Artist subscription";
            $status_code = SUCCESSCODE;
        }
     
        return response([
                 'data'        => $data,
                 'message'     => $message,
                 'status_code' => $status_code
             ], $status_code);
    }

    /* Fetch Promotion*/
    public function get_promotion(Request $request, $artist_id)
    {
        $message =  "Failed to fetch artist promotion";
        $status_code = BADREQUEST;
      
        $data= DB::table('promotions')
                 ->select(
                     'id',
                     'starts_at',
                     'expires_at',
                     'price',
                     'user_id',
                 )->where('user_id', $artist_id)->first();
      
        if (isset($data)) {
            $message = "Artist promotion";
            $status_code = SUCCESSCODE;
        }
      
        return response([
                  'data'        => $data,
                  'message'     => $message,
                  'status_code' => $status_code
              ], $status_code);
    }

    /* Edit subscription */
    public function update(Request $request, $artist_id, $subscription_id)
    {
        $data = [];
        $message     =  '';
        $status_code = '';
   
        $subscription_data = Subscription::find($subscription_id);
 
    
        if ($subscription_data) {
            try {
                $update = [];
                if (isset($request->subscription_type_id)) {
                    $update['subscription_type_id'] = $request->subscription_type_id;
                }
                if (isset($request->price)) {
                    $update['price'] = $request->price;
                }
                 
                $update['updated_at'] = date("Y-m-d H:i:s");
 
                if (count($update) != 0) {
                    DB::table('subscriptions')->where('id', $subscription_id)->where('user_id', $artist_id)->update($update);
                }
                $data= DB::table('subscriptions')
                         ->select(
                             'id',
                             'subscription_type_id',
                             'price',
                             'user_id',
                         )->where('id', $subscription_id)->where('user_id', $artist_id)->get()->first();
                $message = "Artist subscription updated";
                $status_code = SUCCESSCODE;
            } catch (Exception $e) {
                $data=[];
                $message = "Failed to update" . ' ' . $e->getMessage();
                $status_code = BADREQUEST;
            }
        }
 
        return response([
              'data'        => $data,
              'message'     => $message,
              'status_code' => $status_code
          ], $status_code);
    }

     /* Edit promotion */
     public function update_promotion(Request $request, $artist_id, $promotion_id)
     {
         $data = [];
         $message     =  '';
         $status_code = '';
    
         $promotion_data = Promotion::find($promotion_id);
  
         if ($promotion_data) {
             try {
                 $update = [];
                 if (isset($request->starts_at)) {
                     $update['starts_at'] = $request->starts_at;
                 }
                 if (isset($request->expires_at)) {
                    $update['expires_at'] = $request->expires_at;
                }
                 if (isset($request->price)) {
                     $update['price'] = $request->price;
                 }
                  
                 $update['updated_at'] = date("Y-m-d H:i:s");
  
                 if (count($update) != 0) {
                     DB::table('promotions')->where('id', $promotion_id)->where('user_id', $artist_id)->update($update);
                 }
                 $data= DB::table('promotions')
                          ->select(
                              'id',
                              'starts_at',
                              'expires_at',
                              'price',
                              'user_id',
                          )->where('id', $promotion_id)->where('user_id', $artist_id)->get()->first();

                 $message = "Artist promotion updated";
                 $status_code = SUCCESSCODE;
             } catch (Exception $e) {
                 $data=[];
                 $message = "Failed to update" . ' ' . $e->getMessage();
                 $status_code = BADREQUEST;
             }
         }
  
         return response([
               'data'        => $data,
               'message'     => $message,
               'status_code' => $status_code
           ], $status_code);
     }
}
