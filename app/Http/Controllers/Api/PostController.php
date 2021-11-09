<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Post;


class PostController extends Controller
{
    public function add(Request $request)
    {
        $data = [];
        $message = __('user.post_failed');
        $status_code = BADREQUEST;

        $current_user = get_user();

        $post_data['when_to_post_id'] = $request->when_to_post_id;
        $post_data['who_can_see_post_id'] = $request->who_can_see_post_id;
        $post_data['user_id'] = $current_user->id;

        switch ($request->post_type_id) {
        case 1:
            $post_data['title'] = $request->title;
            $post_data['content'] = $request->content;
            $post = Post::create($post_data);
            break;

        case 2:
            $post_data['image'] = $request->image;
            $post_data['content'] = $request->content;
            $post = Post::create($post_data);
            break;

        case 3:
            $post_data['video'] = $request->video;
            $post_data['content'] = $request->content;
            $post = Post::create($post_data);
            break;
            
        case 4:
            $post_data['audio'] = $request->audio;
            $post_data['content'] = $request->content;
            $post = Post::create($post_data);
            break;
            
        case 5:
            $post_data['live_stream'] = $request->live_stream;
            $post_data['content'] = $request->content;
            $post = Post::create($post_data);
            break;
        }
      
        if (!empty($post)) {
            $data = $post;
            $status_code = SUCCESSCODE;
            $message = __('user.post_success');
        }
        
        return response([
            'data' => $data,
            'message' => $message,
            'status_code' => $status_code,
        ], $status_code);
    }
}
