<?php

use Google\Auth\Credentials\GCECredentials;
use Illuminate\Support\Facades\Auth;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Storage;
use App\Models\ActivityLog;
use App\Models\Subscription;
use App\Models\UserSubscription;
use App\Models\Payment;

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