<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;

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
        $widget_data['Total Gross Profits'] = Payment::select('amount')->get()->sum('amount');

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
