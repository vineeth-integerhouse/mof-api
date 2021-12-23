<?php

use Google\Auth\Credentials\GCECredentials;
use Illuminate\Support\Facades\Auth;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Storage;
use App\Models\ActivityLog;
use App\Models\Subscription;
use App\Models\UserSubscription;
use App\Models\Payment;
use App\Models\Post;

function get_user()
{
    return Auth::user();
}

function admin_profile_view_count($start_date, $end_date)
{
    $profile_views= ActivityLog::select('profile_impressions') 
            ->where('activity_type', 'Profile Views')
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->get()
            ->sum('profile_impressions');
            if (!empty($profile_views)) {
            $performance['Profile Views']= $profile_views;
            } else {
            $performance['Profile Views']=0;
            }
    return  $performance['Profile Views'];
}
function admin_fans_count($start_date, $end_date)
{
    $count_of_fans=0;
    $subscription= Subscription::get()->toArray();
    foreach ($subscription as $type) {
    $count_of_fans+= UserSubscription::where('status', '1')
                ->where('subscribe_id', $type['id'])
                ->whereDate('created_at', '>=', $start_date)
                ->whereDate('created_at', '<=', $end_date)->count();
            }
    return $count_of_fans ;

}
function admin_lost_fans_count($start_date, $end_date)
{
    $count_of_lost_fans=0;
    $subscription= Subscription::get()->toArray();
    foreach ($subscription as $type) {
    $count_of_lost_fans+= UserSubscription::where('status', '0')
                ->where('subscribe_id', $type['id'])
                ->whereDate('created_at', '>=', $start_date)
                ->whereDate('created_at', '<=', $end_date)->count();
    }
    return $count_of_lost_fans;
}
function admin_earnings_count($start_date, $end_date)
{
    return $earnings['Total Earned'] = Payment::select('amount')
                ->where('payin_payout', 'Payouts')
                ->where('status', 'Paid')
                ->whereDate('created_at', '>=', $start_date)
                ->whereDate('created_at', '<=', $end_date)
                ->get()->sum('amount'); 
}
function admin_gross_revenue($start_date, $end_date)
{
    $total_gross_revenue = Payment::select('amount')
    ->where('payin_payout','Payin')
    ->whereDate('created_at', '>=', $start_date) 
    ->whereDate('created_at', '<=', $end_date)
    ->get()->sum('amount');
    return $total_gross_revenue;
}
function artist_earnings_count($user_id, $start_date, $end_date)
{
    $total_earned = Payment::select('amount')
        ->where('payee', $user_id)
        ->where('payin_payout', 'Payouts')
        ->where('status', 'Paid')
        ->whereDate('created_at', '>=', $start_date)
        ->whereDate('created_at', '<=', $end_date)
        ->get()->sum('amount');
    return $total_earned;
}
function artist_fans_count($user_id, $start_date, $end_date)
{
    $subscription= Subscription::where('user_id', $user_id)
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
    return $count_of_fans;
}
function artist_profile_impression_count($user_id, $start_date, $end_date)
{
    $impression=ActivityLog::where('artist_id', $user_id)
        ->where('activity_type', 'Profile Views')
        ->whereDate('created_at', '>=', $start_date)
        ->whereDate('created_at', '<=', $end_date)
        ->get('profile_impressions')
        ->first();
    if (!empty($impression)) {
        $widget_data['Total Profile Impressions']= $impression['profile_impressions'];
    } else {
        $widget_data['Total Profile Impressions']=0;
    }
    return $widget_data['Total Profile Impressions'];
}
function artist_comment_count($user_id, $start_date, $end_date)
{
    $count_of_comments=0;
    $post_comment= Post::with('comment')
            ->where('user_id', $user_id)
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->get()
            ->toArray();
    foreach ($post_comment as $type) {
        $count_of_comments+= count($type['comment']);
    }
    return $count_of_comments;
}
function artist_like_count($user_id, $start_date, $end_date)
{
    $count_of_likes=0;
    $post_like= Post::with('like')
            ->where('user_id', $user_id)
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->get()
            ->toArray();
  foreach ($post_like as $type) {
      $count_of_likes+= count($type['like']);
  }
  return $count_of_likes;
}