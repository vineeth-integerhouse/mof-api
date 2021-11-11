<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\GenreType;
use App\Models\PostType;
use App\Models\Role;
use App\Models\SubscriptionType;
use App\Models\WhenToPost;
use App\Models\WhoCanSeePost;

class SeedController extends Controller
{
    public function role(Request $request)
    {
        $data = [];
        $message = __('seeder.seed_fail');
        $status_code = BADREQUEST;

        $seed_details = Role::select('id', 'role_name')->get();

        if (isset($seed_details)) {
            $message = __('seeder.seed_success');
            $status_code = SUCCESSCODE;
            $data = $seed_details;
        }
        return response([
            'data' => $data,
            'message' => $message,
            'status_code' => $status_code
        ], $status_code);
    }


    public function genre_type(Request $request)
    {
        $data = [];
        $message = __('seeder.seed_fail');
        $status_code = BADREQUEST;

        $seed_details = GenreType::select('id', 'genre_option')->get();

        if (isset($seed_details)) {
            $message = __('seeder.seed_success');
            $status_code = SUCCESSCODE;
            $data = $seed_details;
        }
        return response([
            'data' => $data,
            'message' => $message,
            'status_code' => $status_code
        ], $status_code);
    }

    public function post_type(Request $request)
    {
        $data = [];
        $message = __('seeder.seed_fail');
        $status_code = BADREQUEST;

        $seed_details = PostType::select('id', 'post_option')->get();

        if (isset($seed_details)) {
            $message = __('seeder.seed_success');
            $status_code = SUCCESSCODE;
            $data = $seed_details;
        }
        return response([
            'data' => $data,
            'message' => $message,
            'status_code' => $status_code
        ], $status_code);
    }

    public function subscription_type(Request $request)
    {
        $data = [];
        $message = __('seeder.seed_fail');
        $status_code = BADREQUEST;

        $seed_details = SubscriptionType::select('id', 'subscription_type')->get();

        if (isset($seed_details)) {
            $message = __('seeder.seed_success');
            $status_code = SUCCESSCODE;
            $data = $seed_details;
        }
        return response([
            'data' => $data,
            'message' => $message,
            'status_code' => $status_code
        ], $status_code);
    }

    public function when_to_post(Request $request)
    {
        $data = [];
        $message = __('seeder.seed_fail');
        $status_code = BADREQUEST;

        $seed_details = WhenToPost::select('id', 'whentopost_option')->get();

        if (isset($seed_details)) {
            $message = __('seeder.seed_success');
            $status_code = SUCCESSCODE;
            $data = $seed_details;
        }
        return response([
            'data' => $data,
            'message' => $message,
            'status_code' => $status_code
        ], $status_code);
    }

    public function who_can_see_post(Request $request)
    {
        $data = [];
        $message = __('seeder.seed_fail');
        $status_code = BADREQUEST;

        $seed_details = WhoCanSeePost::select('id', 'whocanseepost_option')->get();

        if (isset($seed_details)) {
            $message = __('seeder.seed_success');
            $status_code = SUCCESSCODE;
            $data = $seed_details;
        }
        return response([
            'data' => $data,
            'message' => $message,
            'status_code' => $status_code
        ], $status_code);
    }

}
