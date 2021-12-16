<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Payment;
use App\Models\Post;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Database\Eloquent\Builder;

class DashboardController extends Controller
{
    /* Admin Statictcs */
    
    public function admin_statistcs(Request $request)
    {
        $widget_data = [];
        $message = __('user.statistics_failed');
        $status_code = BADREQUEST;

        $current_user=get_user();

        $widget_data['Registered Users']  =  User::users_count(USER_ROLE_USER);
        $widget_data['Registered Artists']  =  User::artists_count(USER_ROLE_ARTIST);
        $widget_data['Total Gross Revenue'] = Payment::select('amount')->where('payin_payout', 'Payin')->get()->sum('amount');

        $total_payout=Payment::select('amount')->where('payin_payout', 'Payouts')->get()->sum('amount');
        $widget_data['Total Gross Profits'] =   $widget_data['Total Gross Revenue'] - $total_payout;


        $widget_data['current_user'] = User::select(
            'id',
            'role_id',
            'name',
            'email',
        )->where('id', $current_user->id)->get()->first();
        if ($widget_data) {
            $message =   __('user.statistics_success');
            $status_code = SUCCESSCODE;
        }
        return response([
            'data'        => $widget_data,
            'message'     => $message,
            'status_code' => $status_code
        ]);
    }
    
    public function dashboard_statistcs(Request $request)
    {
        $widget_data = [];
        $message = __('user.statistics_failed');
        $status_code = BADREQUEST;

        $current_user=get_user();

        if ($request->input('filter_option') == 'all_time') {
            $subscription= Subscription::where('user_id', $current_user->id)->get()->toArray();

            $count_of_fans=0;
            foreach ($subscription as $type) {
                $count_of_fans+= UserSubscription::where('status', '1')->where('subscribe_id', $type['id'])->count();
            }
            $widget_data['Total Fans']=  $count_of_fans;

            $impression=ActivityLog::where('artist_id', $current_user->id)->get('profile_impressions')->first();
            if (!empty($impression)) {
                $widget_data['Total Profile Impressions']= $impression['profile_impressions'];
            } else {
                $widget_data['Total Profile Impressions']=0;
            }

            $count_of_comments=0;
            $count_of_likes=0;
            $post_comment= Post::with('comment')->where('user_id', $current_user->id)->get()->toArray();

            foreach ($post_comment as $type) {
                $count_of_comments+= count($type['comment']);
            }

            $post_like= Post::with('like')->where('user_id', $current_user->id)->get()->toArray();

            foreach ($post_like as $type) {
                $count_of_likes+= count($type['like']);
            }

            if (($widget_data['Total Profile Impressions'])>0) {
                $engagement_rate=(($count_of_comments+$count_of_likes)/$widget_data['Total Profile Impressions'])*100;
                $widget_data['Engagement Rate'] = $engagement_rate ;
            } else {
                $widget_data['Engagement Rate'] = 0;
            }

            $widget_data['Total Earned'] = Payment::select('amount')
            ->where('payee', $current_user->id)
            ->where('payin_payout', 'Payouts')
            ->where('status', 'Paid')
            ->get()->sum('amount');
        } elseif ($request->input('filter_option') == 'last_7days') {
            $start_date = date('Y-m-d', strtotime('-7 days'));
            $end_date = date('Y-m-d');

            $subscription= Subscription::where('user_id', $current_user->id)
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)->get()->toArray();

            $count_of_fans=0;
            foreach ($subscription as $type) {
                $count_of_fans+= UserSubscription::where('status', '1')
                ->where('subscribe_id', $type['id'])
                ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->count();
            }
            $widget_data['Total Fans']=  $count_of_fans;
        
            $impression=ActivityLog::where('artist_id', $current_user->id)
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->get('profile_impressions')
            ->first();
            if (!empty($impression)) {
                $widget_data['Total Profile Impressions']= $impression['profile_impressions'];
            } else {
                $widget_data['Total Profile Impressions']=0;
            }

            $count_of_comments=0;
            $count_of_likes=0;
            $post_comment= Post::with('comment')
            ->where('user_id', $current_user->id)
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->get()
            ->toArray();

            foreach ($post_comment as $type) {
                $count_of_comments+= count($type['comment']);
            }

            $post_like= Post::with('like')
            ->where('user_id', $current_user->id)
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->get()
            ->toArray();

            foreach ($post_like as $type) {
                $count_of_likes+= count($type['like']);
            }

            if (($widget_data['Total Profile Impressions'])>0) {
                $engagement_rate=(($count_of_comments+$count_of_likes)/$widget_data['Total Profile Impressions'])*100;
                $widget_data['Engagement Rate'] = $engagement_rate ;
            } else {
                $widget_data['Engagement Rate'] = 0;
            }

            $widget_data['Total Earned'] = Payment::select('amount')
            ->where('payee', $current_user->id)
            ->where('payin_payout', 'Payouts')
            ->where('status', 'Paid')
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->get()->sum('amount');
        } elseif ($request->input('filter_option') == 'this_month') {
            $year = date('Y');
            $month = date('m');

            $subscription= Subscription::where('user_id', $current_user->id)
              ->whereYear('created_at', '=', $year)
              ->whereMonth('created_at', '=', $month)->get()->toArray();
  
            $count_of_fans=0;
            foreach ($subscription as $type) {
                $count_of_fans+= UserSubscription::where('status', '1')
                  ->where('subscribe_id', $type['id'])
                  ->whereYear('created_at', '=', $year)
                  ->whereMonth('created_at', '=', $month)
                  ->count();
            }
            $widget_data['Total Fans']=  $count_of_fans;
          
            $impression=ActivityLog::where('artist_id', $current_user->id)
            ->whereYear('created_at', '=', $year)
            ->whereMonth('created_at', '=', $month)
              ->get('profile_impressions')
              ->first();
            if (!empty($impression)) {
                $widget_data['Total Profile Impressions']= $impression['profile_impressions'];
            } else {
                $widget_data['Total Profile Impressions']=0;
            }
  
            $count_of_comments=0;
            $count_of_likes=0;
            $post_comment= Post::with('comment')
              ->where('user_id', $current_user->id)
              ->whereYear('created_at', '=', $year)
              ->whereMonth('created_at', '=', $month)
              ->get()
              ->toArray();
  
            foreach ($post_comment as $type) {
                $count_of_comments+= count($type['comment']);
            }
  
            $post_like= Post::with('like')
              ->where('user_id', $current_user->id)
              ->whereYear('created_at', '=', $year)
              ->whereMonth('created_at', '=', $month)
              ->get()
              ->toArray();
  
            foreach ($post_like as $type) {
                $count_of_likes+= count($type['like']);
            }
  
            if (($widget_data['Total Profile Impressions'])>0) {
                $engagement_rate=(($count_of_comments+$count_of_likes)/$widget_data['Total Profile Impressions'])*100;
                $widget_data['Engagement Rate'] = $engagement_rate ;
            } else {
                $widget_data['Engagement Rate'] = 0;
            }
  
            $widget_data['Total Earned'] = Payment::select('amount')
              ->where('payee', $current_user->id)
              ->where('payin_payout', 'Payouts')
              ->where('status', 'Paid')
              ->whereYear('created_at', '=', $year)
              ->whereMonth('created_at', '=', $month)
              ->get()->sum('amount');
        } elseif ($request->input('filter_option') == 'this_year') {
            $year = date('Y');
        
            $subscription= Subscription::where('user_id', $current_user->id)
              ->whereYear('created_at', '=', $year)
              ->get()->toArray();
  
            $count_of_fans=0;
            foreach ($subscription as $type) {
                $count_of_fans+= UserSubscription::where('status', '1')
                  ->where('subscribe_id', $type['id'])
                  ->whereYear('created_at', '=', $year)
                  ->count();
            }
            $widget_data['Total Fans']=  $count_of_fans;
          
            $impression=ActivityLog::where('artist_id', $current_user->id)
            ->whereYear('created_at', '=', $year)
              ->get('profile_impressions')
              ->first();
            if (!empty($impression)) {
                $widget_data['Total Profile Impressions']= $impression['profile_impressions'];
            } else {
                $widget_data['Total Profile Impressions']=0;
            }
  
            $count_of_comments=0;
            $count_of_likes=0;
            $post_comment= Post::with('comment')
              ->where('user_id', $current_user->id)
              ->whereYear('created_at', '=', $year)
              ->get()
              ->toArray();
  
            foreach ($post_comment as $type) {
                $count_of_comments+= count($type['comment']);
            }
  
            $post_like= Post::with('like')
              ->where('user_id', $current_user->id)
              ->whereYear('created_at', '=', $year)
              ->get()
              ->toArray();
  
            foreach ($post_like as $type) {
                $count_of_likes+= count($type['like']);
            }
  
            if (($widget_data['Total Profile Impressions'])>0) {
                $engagement_rate=(($count_of_comments+$count_of_likes)/$widget_data['Total Profile Impressions'])*100;
                $widget_data['Engagement Rate'] = $engagement_rate ;
            } else {
                $widget_data['Engagement Rate'] = 0;
            }
  
            $widget_data['Total Earned'] = Payment::select('amount')
              ->where('payee', $current_user->id)
              ->where('payin_payout', 'Payouts')
              ->where('status', 'Paid')
              ->whereYear('created_at', '=', $year)
              ->get()->sum('amount');
        } elseif ($request->input('filter_option') == 'choose_date') {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
        
            $subscription= Subscription::where('user_id', $current_user->id)
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
              ->get()->toArray();
  
            $count_of_fans=0;
            foreach ($subscription as $type) {
                $count_of_fans+= UserSubscription::where('status', '1')
                  ->where('subscribe_id', $type['id'])
                  ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
                  ->count();
            }
            $widget_data['Total Fans']=  $count_of_fans;
          
            $impression=ActivityLog::where('artist_id', $current_user->id)
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
              ->get('profile_impressions')
              ->first();
            if (!empty($impression)) {
                $widget_data['Total Profile Impressions']= $impression['profile_impressions'];
            } else {
                $widget_data['Total Profile Impressions']=0;
            }
  
            $count_of_comments=0;
            $count_of_likes=0;
            $post_comment= Post::with('comment')
              ->where('user_id', $current_user->id)
              ->whereDate('created_at', '>=', $start_date)
              ->whereDate('created_at', '<=', $end_date)
              ->get()
              ->toArray();
  
            foreach ($post_comment as $type) {
                $count_of_comments+= count($type['comment']);
            }
  
            $post_like= Post::with('like')
              ->where('user_id', $current_user->id)
              ->whereDate('created_at', '>=', $start_date)
              ->whereDate('created_at', '<=', $end_date)
              ->get()
              ->toArray();
  
            foreach ($post_like as $type) {
                $count_of_likes+= count($type['like']);
            }
            if (($widget_data['Total Profile Impressions'])>0) {
                $engagement_rate=(($count_of_comments+$count_of_likes)/$widget_data['Total Profile Impressions'])*100;
                $widget_data['Engagement Rate'] = $engagement_rate ;
            } else {
                $widget_data['Engagement Rate'] = 0;
            }
            
            $widget_data['Total Earned'] = Payment::select('amount')
              ->where('payee', $current_user->id)
              ->where('payin_payout', 'Payouts')
              ->where('status', 'Paid')
              ->whereDate('created_at', '>=', $start_date)
              ->whereDate('created_at', '<=', $end_date)
              ->get()->sum('amount');
        }
    
        $widget_data['current_user'] = User::select(
            'id',
            'role_id',
            'name',
            'email',
        )->where('id', $current_user->id)->get()->first();
        if ($widget_data) {
            $message =   __('user.statistics_success');
            $status_code = SUCCESSCODE;
        }
    
        return response([
            'data'        => $widget_data,
            'message'     => $message,
            'status_code' => $status_code
        ]);
    }

    public function profile_views(Request $request)
    {
        $widget_data = [];
        $message = __('user.profile_views_failed');
        $status_code = BADREQUEST;

        $current_user=get_user();

        if ($request->input('filter_option') == 'all_time') {
            $profile_views= ActivityLog::where('activity_type', 'Profile Views')
                            ->where('artist_id', $current_user->id)
                            ->get('profile_impressions')
                            ->first();
                            
            if (!empty($profile_views)) {
                $widget_data['Profile Views']= $profile_views['profile_impressions'];
            } else {
                $widget_data['Profile Views']=0;
            }
        } elseif ($request->input('filter_option') == 'last_7days') {
            $start_date = date('Y-m-d', strtotime('-7 days'));
            $end_date = date('Y-m-d');

            $profile_views= ActivityLog::where('activity_type', 'Profile Views')
                             ->whereDate('created_at', '>=', $start_date)
                             ->whereDate('created_at', '<=', $end_date)
                             ->where('artist_id', $current_user->id)
                             ->get('profile_impressions')
                             ->first();
               
            if (!empty($profile_views)) {
                $widget_data['Profile Views']= $profile_views['profile_impressions'];
            } else {
                $widget_data['Profile Views']=0;
            }
        } elseif ($request->input('filter_option') == 'this_month') {
            $year = date('Y');
            $month = date('m');

            $profile_views= ActivityLog::where('activity_type', 'Profile Views')
                             ->whereYear('created_at', '=', $year)
                             ->whereMonth('created_at', '=', $month)
                             ->where('artist_id', $current_user->id)
                             ->get('profile_impressions')
                             ->first();

            if (!empty($profile_views)) {
                $widget_data['Profile Views']= $profile_views['profile_impressions'];
            } else {
                $widget_data['Profile Views']=0;
            }
        } elseif ($request->input('filter_option') == 'this_year') {
            $year = date('Y');
        
            $profile_views= ActivityLog::where('activity_type', 'Profile Views')
                             ->whereYear('created_at', '=', $year)
                             ->where('artist_id', $current_user->id)
                             ->get('profile_impressions')
                             ->first();

            if (!empty($profile_views)) {
                $widget_data['Profile Views']= $profile_views['profile_impressions'];
            } else {
                $widget_data['Profile Views']=0;
            }
        } elseif ($request->input('filter_option') == 'choose_date') {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
        
            $profile_views= ActivityLog::where('activity_type', 'Profile Views')
                            ->whereDate('created_at', '>=', $start_date)
                            ->whereDate('created_at', '<=', $end_date)
                            ->where('artist_id', $current_user->id)
                            ->get('profile_impressions')
                            ->first();

            if (!empty($profile_views)) {
                $widget_data['Profile Views']= $profile_views['profile_impressions'];
            } else {
                $widget_data['Profile Views']=0;
            }
        }
    
        $widget_data['current_user'] = User::select(
            'id',
            'role_id',
            'name',
            'email',
        )->where('id', $current_user->id)->get()->first();
        if ($widget_data) {
            $message =   __('user.profile_views_success');
            $status_code = SUCCESSCODE;
        }
    
        return response([
            'data'        => $widget_data,
            'message'     => $message,
            'status_code' => $status_code
        ]);
    }

    public function fans(Request $request)
    {
        $widget_data = [];
        $message = __('user.fans_failed');
        $status_code = BADREQUEST;

        $current_user=get_user();

        if ($request->input('filter_option') == 'all_time') {
            $subscription= Subscription::where('user_id', $current_user->id)->get()->toArray();

            $count_of_fans=0;
            $count_of_lost_fans=0;
            foreach ($subscription as $type) {
                $count_of_fans+= UserSubscription::where('status', '1')->where('subscribe_id', $type['id'])->count();
                $count_of_lost_fans+= UserSubscription::where('status', '0')->where('subscribe_id', $type['id'])->count();
            }
            $widget_data['Fans']=  $count_of_fans;
            $widget_data['Lost Fans']=  $count_of_lost_fans;
        } elseif ($request->input('filter_option') == 'last_7days') {
            $start_date = date('Y-m-d', strtotime('-7 days'));
            $end_date = date('Y-m-d');

            $subscription= Subscription::where('user_id', $current_user->id)->get()->toArray();

            $count_of_fans=0;
            $count_of_lost_fans=0;
            foreach ($subscription as $type) {
                $count_of_fans+= UserSubscription::where('status', '1')
                                 ->where('subscribe_id', $type['id'])
                                 ->whereDate('created_at', '>=', $start_date)
                                 ->whereDate('created_at', '<=', $end_date)
                                 ->count();
                $count_of_lost_fans+= UserSubscription::where('status', '0')
                                  ->where('subscribe_id', $type['id'])
                                  ->whereDate('created_at', '>=', $start_date)
                                  ->whereDate('created_at', '<=', $end_date)
                                  ->count();
            }
            $widget_data['Fans']=  $count_of_fans;
            $widget_data['Lost Fans']=  $count_of_lost_fans;
        } elseif ($request->input('filter_option') == 'this_month') {
            $year = date('Y');
            $month = date('m');

            $subscription= Subscription::where('user_id', $current_user->id)->get()->toArray();

            $count_of_fans=0;
            $count_of_lost_fans=0;
            foreach ($subscription as $type) {
                $count_of_fans+= UserSubscription::where('status', '1')
                                 ->where('subscribe_id', $type['id'])
                                 ->whereYear('created_at', '=', $year)
                                 ->whereMonth('created_at', '=', $month)
                                 ->count();
                $count_of_lost_fans+= UserSubscription::where('status', '0')
                                  ->where('subscribe_id', $type['id'])
                                  ->whereYear('created_at', '=', $year)
                                  ->whereMonth('created_at', '=', $month)
                                  ->count();
            }
            $widget_data['Fans']=  $count_of_fans;
            $widget_data['Lost Fans']=  $count_of_lost_fans;
        } elseif ($request->input('filter_option') == 'this_year') {
            $year = date('Y');

            $subscription= Subscription::where('user_id', $current_user->id)->get()->toArray();

            $count_of_fans=0;
            $count_of_lost_fans=0;
            foreach ($subscription as $type) {
                $count_of_fans+= UserSubscription::where('status', '1')
                                 ->where('subscribe_id', $type['id'])
                                 ->whereYear('created_at', '=', $year)
                                 ->count();
                $count_of_lost_fans+= UserSubscription::where('status', '0')
                                  ->where('subscribe_id', $type['id'])
                                  ->whereYear('created_at', '=', $year)
                                  ->count();
            }
            $widget_data['Fans']=  $count_of_fans;
            $widget_data['Lost Fans']=  $count_of_lost_fans;
        } elseif ($request->input('filter_option') == 'choose_date') {
            $start_date = $request->start_date;
            $end_date = $request->end_date;

            $subscription= Subscription::where('user_id', $current_user->id)->get()->toArray();

            $count_of_fans=0;
            $count_of_lost_fans=0;
            foreach ($subscription as $type) {
                $count_of_fans+= UserSubscription::where('status', '1')
                                 ->where('subscribe_id', $type['id'])
                                 ->whereDate('created_at', '>=', $start_date)
                            ->whereDate('created_at', '<=', $end_date)
                                 ->count();
                $count_of_lost_fans+= UserSubscription::where('status', '0')
                                  ->where('subscribe_id', $type['id'])
                                  ->whereDate('created_at', '>=', $start_date)
                            ->whereDate('created_at', '<=', $end_date)
                                  ->count();
            }
            $widget_data['Fans']=  $count_of_fans;
            $widget_data['Lost Fans']=  $count_of_lost_fans;
        }
    
        $widget_data['current_user'] = User::select(
            'id',
            'role_id',
            'name',
            'email',
        )->where('id', $current_user->id)->get()->first();
        if ($widget_data) {
            $message =   __('user.fans_success');
            $status_code = SUCCESSCODE;
        }
    
        return response([
            'data'        => $widget_data,
            'message'     => $message,
            'status_code' => $status_code
        ]);
    }

    public function earnings(Request $request)
    {
        $widget_data = [];
        $message = __('user.earnings_failed');
        $status_code = BADREQUEST;

        $current_user=get_user();

        if ($request->input('filter_option') == 'all_time') {
            $widget_data['Total Earned'] = Payment::select('amount')
            ->where('payee', $current_user->id)
            ->where('payin_payout', 'Payouts')
            ->where('status', 'Paid')
            ->get()->sum('amount');
        } elseif ($request->input('filter_option') == 'last_7days') {
            $start_date = date('Y-m-d', strtotime('-7 days'));
            $end_date = date('Y-m-d');

            $widget_data['Total Earned'] = Payment::select('amount')
            ->where('payee', $current_user->id)
            ->where('payin_payout', 'Payouts')
            ->where('status', 'Paid')
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->get()->sum('amount');
        } elseif ($request->input('filter_option') == 'this_month') {
            $year = date('Y');
            $month = date('m');

            $widget_data['Total Earned'] = Payment::select('amount')
            ->where('payee', $current_user->id)
            ->where('payin_payout', 'Payouts')
            ->where('status', 'Paid')
            ->whereYear('created_at', '=', $year)
            ->whereMonth('created_at', '=', $month)
            ->get()->sum('amount');
        } elseif ($request->input('filter_option') == 'this_year') {
            $year = date('Y');

            $widget_data['Total Earned'] = Payment::select('amount')
            ->where('payee', $current_user->id)
            ->where('payin_payout', 'Payouts')
            ->where('status', 'Paid')
            ->whereYear('created_at', '=', $year)
            ->get()->sum('amount');
        } elseif ($request->input('filter_option') == 'choose_date') {
            $start_date = $request->start_date;
            $end_date = $request->end_date;

            $widget_data['Total Earned'] = Payment::select('amount')
            ->where('payee', $current_user->id)
            ->where('payin_payout', 'Payouts')
            ->where('status', 'Paid')
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->get()->sum('amount');
        }
    
        $widget_data['current_user'] = User::select(
            'id',
            'role_id',
            'name',
            'email',
        )->where('id', $current_user->id)->get()->first();
        if ($widget_data) {
            $message =   __('user.earnings_success');
            $status_code = SUCCESSCODE;
        }
    
        return response([
            'data'        => $widget_data,
            'message'     => $message,
            'status_code' => $status_code
        ]);
    }
}
