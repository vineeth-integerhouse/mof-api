<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\GenreType;
use App\Models\PostType;
use App\Models\Role;
use App\Models\SocialProfileType;
use App\Models\SubscriptionType;
use App\Models\WhenToPost;
use App\Models\WhoCanSeePost;
use App\Models\NotificationType;

class SeedController extends Controller
{
    /* Role Seeder */

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


    /* Genre Type Seeder */

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

     /* Post Type Seeder */

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

     /* Subscription Type Seeder */

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

     /* WhenToPost Type Seeder */

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

     /* WhoCanSeePost Type Seeder */

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

      /* Profile Type Seeder */

      public function profile_type(Request $request)
      {
          $data = [];
          $message = __('seeder.seed_fail');
          $status_code = BADREQUEST;
  
          $seed_details = SocialProfileType::select('id', 'profile_type')->get();
  
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

    /* Notification Type Seeder */

    public function notification_type(Request $request)
    {
        $data = [];
        $message = __('seeder.seed_fail');
        $status_code = BADREQUEST;

        $seed_details = NotificationType::select('id', 'notification_type')->get();

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
