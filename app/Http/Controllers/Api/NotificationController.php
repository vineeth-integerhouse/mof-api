<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function notification_settings(Request $request)
    {
        $data = [];
        $message = __('notification.notification_settings_failed');
        $status_code = BADREQUEST;

        $current_user = get_user();

        $validate_data = Validator::make($request->all(), [
            'notification_id' => 'required',
            'status' => 'required',
        ]);

        if ($validate_data->fails()) {
            $errors = $validate_data->errors();
            $message =  implode(', ', $errors->all());
        } else {
            $inserted_data = UserNotification::updateOrCreate(
                [  'notification_id'=> $request->notification_id,
                'user_id' => $current_user->id],
                
                [
                    'user_id' => $current_user->id,
                    'status' => $request->status
                ]
            );

            $data= UserNotification::select(
                'id',
                'notification_id',
                'status',
            )->where('id', $inserted_data->id)->get()->first();

            $message = __('notification.notification_settings');
            $status_code = SUCCESSCODE;
        }

        return response([
            'data' => $data,
            'message' => $message,
            'status_code' => $status_code,
        ], $status_code);
    }

    public function get(Request $request)
    {
        $current_user = get_user();

        $data = [];
        $message = __('user.user_doesnt_exist');
        $status_code = BADREQUEST;

        if ($current_user) {
            $data= UserNotification::select(
                'id',
                'notification_id',
                'status',
            )->where('user_id', $current_user->id)->get();

            $message = __('notification.details');
            $status_code = SUCCESSCODE;
        }

        return response([
            'data' => $data,
            'message' => $message,
            'status_code' => $status_code
        ], $status_code);
    }
}
