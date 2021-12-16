<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Payment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class DashboardController extends Controller
{
    /* Admin Statictcs */
    
    public function admin_statistcs(Request $request)
    {
        $widget_data = [];
        $data      = [];
        $message = __('user.statistics_failed');
        $status_code = BADREQUEST;

        $current_user=get_user();

        if ($request->input('filter_option') == 'all_time') {
            $user_count  = User::with('role')
            ->whereHas('role', function (Builder $query)  {
                $query->select('id')->where('role_name', 'User');
            })->count();
         $widget_data['Registered Users']  =  $user_count;
         $artist_count  = User::with('role')
         ->whereHas('role', function (Builder $query)  {
             $query->select('id')->where('role_name', 'Artist');
         })->count();
         $widget_data['Registered Artists']  =  $artist_count;
        $widget_data['Total Gross Revenue'] = Payment::select('amount')->where('payin_payout','Payin')->get()->sum('amount');

        $total_payout=Payment::select('amount')->where('payin_payout','Payouts')->get()->sum('amount');
        $widget_data['Total Gross Profits'] =   $widget_data['Total Gross Revenue'] - $total_payout;
        } elseif ($request->input('filter_option') == 'today') {

            $start_date = date('Y-m-d');
            $end_date = date('Y-m-d');
            $widget_data['Registered Users']  =  User::users_count(USER_ROLE_USER, $start_date, $end_date);
            $widget_data['Registered Artists']  = User::artists_count(USER_ROLE_ARTIST,  $start_date, $end_date);
            $widget_data['Total Gross Revenue'] = Payment::select('amount')->where('payin_payout','Payin')->whereDate('created_at', '>=', $start_date) ->whereDate('created_at', '<=', $end_date)->get()->sum('amount');
    
            $total_payout=Payment::select('amount')->where('payin_payout','Payouts')->whereDate('created_at', '>=', $start_date) ->whereDate('created_at', '<=', $end_date)->get()->sum('amount');
            $widget_data['Total Gross Profits'] =   $widget_data['Total Gross Revenue'] - $total_payout;
        } elseif ($request->input('filter_option') == 'this_week') {
            $start_date = date('Y-m-d', strtotime('-1 week monday 00:00:00'));
            $end_date = date('Y-m-d', strtotime('sunday 23:59:59'));
            $widget_data['Registered Users']  =  User::users_count(USER_ROLE_USER, $start_date, $end_date);
            $widget_data['Registered Artists']  = User::artists_count(USER_ROLE_ARTIST,  $start_date, $end_date);
            $widget_data['Total Gross Revenue'] = Payment::select('amount')->where('payin_payout','Payin')->whereDate('created_at', '>=', $start_date) ->whereDate('created_at', '<=', $end_date)->get()->sum('amount');
    
            $total_payout=Payment::select('amount')->where('payin_payout','Payouts')->whereDate('created_at', '>=', $start_date) ->whereDate('created_at', '<=', $end_date)->get()->sum('amount');
            $widget_data['Total Gross Profits'] =   $widget_data['Total Gross Revenue'] - $total_payout;
        } elseif ($request->input('filter_option') == 'this_month') {
            $start_date = date("Y-n-j", strtotime("first day of this month"));
            $end_date = date("Y-n-j", strtotime("last day of this month"));
            $widget_data['Registered Users']  =  User::users_count(USER_ROLE_USER, $start_date, $end_date);
            $widget_data['Registered Artists']  = User::artists_count(USER_ROLE_ARTIST,  $start_date, $end_date);
            $widget_data['Total Gross Revenue'] = Payment::select('amount')->where('payin_payout','Payin')->whereDate('created_at', '>=', $start_date) ->whereDate('created_at', '<=', $end_date)->get()->sum('amount');
    
            $total_payout=Payment::select('amount')->where('payin_payout','Payouts')->whereDate('created_at', '>=', $start_date) ->whereDate('created_at', '<=', $end_date)->get()->sum('amount');
            $widget_data['Total Gross Profits'] =   $widget_data['Total Gross Revenue'] - $total_payout;
        } elseif ($request->input('filter_option') == 'six_month') {
            $start_date = date('Y-m-d', strtotime('-6 months 00:00:00'));
            print_r($start_date);
            $end_date = date('Y-m-d', strtotime('sunday 23:59:59'));
            $widget_data['Registered Users']  =  User::users_count(USER_ROLE_USER, $start_date, $end_date);
            $widget_data['Registered Artists']  = User::artists_count(USER_ROLE_ARTIST,  $start_date, $end_date);
            $widget_data['Total Gross Revenue'] = Payment::select('amount')->where('payin_payout','Payin')->whereDate('created_at', '>=', $start_date) ->whereDate('created_at', '<=', $end_date)->get()->sum('amount');
    
            $total_payout=Payment::select('amount')->where('payin_payout','Payouts')->whereDate('created_at', '>=', $start_date) ->whereDate('created_at', '<=', $end_date)->get()->sum('amount');
            $widget_data['Total Gross Profits'] =   $widget_data['Total Gross Revenue'] - $total_payout;
        } elseif ($request->input('filter_option') == 'custom') {
            $start_date = date('Y-m-d', strtotime($request->input('start_date')));
            $end_date =date('Y-m-d', strtotime($request->input('end_date'))) ;
            $widget_data['Registered Users']  =  User::users_count(USER_ROLE_USER, $start_date, $end_date);
            $widget_data['Registered Artists']  = User::artists_count(USER_ROLE_ARTIST,  $start_date, $end_date);
            $widget_data['Total Gross Revenue'] = Payment::select('amount')->where('payin_payout','Payin')->whereDate('created_at', '>=', $start_date) ->whereDate('created_at', '<=', $end_date)->get()->sum('amount');
    
            $total_payout=Payment::select('amount')->where('payin_payout','Payouts')->whereDate('created_at', '>=', $start_date) ->whereDate('created_at', '<=', $end_date)->get()->sum('amount');
            $widget_data['Total Gross Profits'] =   $widget_data['Total Gross Revenue'] - $total_payout;
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
    
    public function dashboard_statistcs(Request $request)
    {
        $widget_data = [];
        $data      = [];
        $post = array();
        $message = __('user.statistics_failed');
        $status_code = BADREQUEST;

        $current_user=get_user();

        $widget_data['Total Fans']=  0;
       

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

        $engagement_rate=(($count_of_comments+$count_of_likes)/$widget_data['Total Profile Impressions'])*100; 
        $widget_data['Engagement Rate'] = $engagement_rate ;

        $widget_data['Total Earned'] = Payment::select('amount')->where('payee', $current_user->id)->get()->sum('amount');

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
}
