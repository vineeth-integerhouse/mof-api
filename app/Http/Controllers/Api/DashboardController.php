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

        $widget_data['Registered Users']  =  User::users_count(USER_ROLE_USER);
        $widget_data['Registered Artists']  =  User::artists_count(USER_ROLE_ARTIST);
        $widget_data['Total Gross Revenue'] = Payment::select('amount')->get()->sum('amount');
        $widget_data['Total Gross Profits'] = 0;

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
