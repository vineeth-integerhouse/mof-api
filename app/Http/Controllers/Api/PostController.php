<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostTag;
use App\Models\User;
use App\Models\UserSubscription;
use Exception;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    /* Add Post */

    public function add(Request $request)
    {
        $data = [];
        $message = __('user.post_failed');
        $status_code = BADREQUEST;

        $current_user = get_user();

        $post_data['post_type_id'] = $request->post_type_id;
        $post_data['when_to_post_id'] = $request->when_to_post_id;
  

        $post_data['who_can_see_post_id'] = $request->who_can_see_post_id;
        $post_data['date'] = $request->date;
        if ($request->time) {
            $post_data['time'] = date("H:i:s", strtotime($request->time));
        } else {
            $post_data['time'] =null;
        }
        $post_data['user_id'] = $current_user->id;
 

        switch ($request->post_type_id) {
        case 1:
            $post_data['title'] = $request->title;
            $post_data['content'] = $request->content;
            break;

        case 2:
            $post_data['title'] = $request->title;
            $post_data['image'] = $request->image;
            $post_data['content'] = $request->content;
            break;

        case 3:
            $post_data['title'] = $request->title;
            $post_data['video'] = $request->video;
            $post_data['content'] = $request->content;
            break;
            
        case 4:
            $post_data['title'] = $request->title;
            $post_data['audio'] = $request->audio;
            $post_data['content'] = $request->content;
            break;
            
        case 5:
            $post_data['title'] = $request->title;
            $post_data['live_stream'] = $request->live_stream;
            $post_data['content'] = $request->content;
            break;
        }

      
        $inserted_data = Post::create($post_data);

        $data= Post::select(
            'id',
            'title',
            'content',
            'image',
            'video',
            'audio',
            'live_stream',
            'when_to_post_id',
            'user_id',
            'who_can_see_post_id',
            'post_type_id',
            'date',
            'created_at',
            DB::raw("DATE_FORMAT(posts.time, '%h:%i %p') as time"),
        )->where('id', $inserted_data->id)->get()->first();

        $tag_id= $request->tag_id;
        $tag_ids = explode(',', $tag_id);
       
        if (isset($tag_ids)) {
            for ($i=0;$i<count($tag_ids);$i++) {
                $tag_data = ['user_id'=>$current_user->id, 'tagged_user_id' => $tag_ids[$i], 'post_id'=>$inserted_data->id];
                PostTag::updateOrCreate($tag_data);
            }
        }
        $data['tag_id']= PostTag::select(
            'post_tags.id',
            'post_tags.tagged_user_id',
            'users.name',
            'users.username',
            'users.profile_pic',
        )->leftJoin('users', 'users.id', '=', 'post_tags.tagged_user_id')
        ->where('user_id', $current_user->id)
        ->where('post_id', $inserted_data->id)->get();

        $status_code = SUCCESSCODE;
        $message = __('user.post_success');
        
        return response([
            'data' => $data,
            'message' => $message,
            'status_code' => $status_code,
        ], $status_code);
    }

    /* Edit Post */

    public function update(Request $request, $post_id)
    {
        $data = [];
        $message = __('user.update_failed');
        $status_code = BADREQUEST;

        $current_user = get_user();

        $post_data = Post::find($post_id);
              
        if ($post_data) {
            try {
                $update = [];
                if (isset($request->when_to_post_id)) {
                    $update['when_to_post_id'] = $request->when_to_post_id;
                }
                if (isset($request->who_can_see_post_id)) {
                    $update['who_can_see_post_id'] = $request->who_can_see_post_id;
                }
 

                switch ($request->post_type_id) {
            case 1:
                $update['title'] = $request->title;
                $update['content'] = $request->content;
            break;

           case 2:
            $update['image'] = $request->image;
            $update['content'] = $request->content;
             break;

           case 3:
            $update['video'] = $request->video;
            $update['content'] = $request->content;
             break;
            
           case 4:
            $update['audio'] = $request->audio;
            $update['content'] = $request->content;
             break;
            
           case 5:
            $update['live_stream'] = $request->live_stream;
            $update['content'] = $request->content;
             break;
        }
         
                DB::table('posts')->where('id', $post_id)->update($update);

        
                $message = __('user.update_success');
                $status_code = SUCCESSCODE;
            } catch (Exception $e) {
                $message = __('user.update_failed') . ' ' . $e->getMessage();
                $status_code = BADREQUEST;
            }
            $data= Post::select(
                'id',
                'title',
                'content',
                'image',
                'video',
                'audio',
                'live_stream',
                'when_to_post_id',
                'user_id',
                'who_can_see_post_id',
                'post_type_id',
                'created_at',
            )->where('id', $post_id)->get()->first();
        }
    
        return response([
        'data'        => $data,
        'message'     => $message,
        'status_code' => $status_code
          ], $status_code);
    }

    /* Delete Post */

    public function delete(Request $request, $post_id)
    {
        $user_data = [];
        $data      = [];
        $message =  __('user.invalid_user');
        $status_code = BADREQUEST;

        $current_user = get_user();

        $post = Post::where('id', $post_id)->first();

        $post_data = Post::where('id', $post_id)->where('user_id', $current_user->id)->delete();
       
        if ($post_data === 1) {
            $data['id']   = $post->id;
            $message = __('user.post_delete_success');
            $status_code = SUCCESSCODE;
        } else {
            $message = __('user.post_delete_failed');
            $status_code = BADREQUEST;
        }
    
        return response([
            'data'        => $data,
            'message'     => $message,
            'status_code' => $status_code
        ], $status_code);
    }

    /* Fetch Post */

    public function get(Request $request, $post_id)
    {
        $message =  __('user.post_fetch failed');
        $status_code = BADREQUEST;

        // $data= Post::withTrashed()->select(
        //     'posts.id',
        //     'posts.title',
        //     'posts.content',
        //     'posts.image',
        //     'posts.video',
        //     'posts.audio',
        //     'posts.live_stream',
        //     'posts.when_to_post_id',
        //     'posts.user_id',
        //     'posts.who_can_see_post_id',
        //     'posts.post_type_id',
        //     'posts.time',
        //     'posts.date',
        //     'posts.deleted_at',
        //     'posts.created_at',
        //     'post_tags.id',
        //     'post_tags.tagged_user_id'

        // )->leftJoin('post_tags', 'post_tags.post_id', '=', 'posts.id')
        // ->where('posts.id', $post_id)->get()->first();

        $post = Post::with(['postTag'])
        ->where('posts.id', $post_id)
        ->first();

        if (isset($post)) {
            $message = __('user.post_fetch_success');
            $status_code = SUCCESSCODE;
        }

        return response([
            'data'        => $post,
            'message'     => $message,
            'status_code' => $status_code
        ], $status_code);
    }

    /* List Post */
     
    public function publish_list(Request $request)
    {
        $data = [];
        $message = __('user.post_fetch failed');
        $status_code = BADREQUEST;

        $current_user = get_user();
 
        $post = Post::with(['user','postTag.taggedUser'])
                    ->where('posts.user_id', $current_user->id)
                    ->where('posts.when_to_post_id', '=', '1')
                    ->orderBy('posts.created_at', 'DESC')
                    ->get();
    
        if (isset($post)) {
            $message = __('user.post_fetch_success');
            $status_code = SUCCESSCODE;
            $data = $post;
        }
        return response([
             'data' => $data,
             'message' => $message,
             'status_code' => $status_code
         ], $status_code);
    }

    public function scheduled_list(Request $request)
    {
        $data = [];
        $message = __('user.post_fetch failed');
        $status_code = BADREQUEST;

        $current_user = get_user();

        $post = Post::with(['user','postTag.taggedUser'])
                    ->where('posts.user_id', $current_user->id)
                    ->where('posts.when_to_post_id', '=', '2')
                    ->orderBy('posts.created_at', 'DESC')
                    ->get();
 
        if (isset($post)) {
            $message = "Scheduled Post";
            $status_code = SUCCESSCODE;
            $data = $post;
        }
        return response([
             'data' => $data,
             'message' => $message,
             'status_code' => $status_code
         ], $status_code);
    }

    public function saved_list(Request $request)
    {
        $data = [];
        $message = __('user.post_fetch failed');
        $status_code = BADREQUEST;
 
        $current_user = get_user();

        $post = Post::with(['user','postTag.taggedUser'])
        ->where('posts.user_id', $current_user->id)
        ->where('posts.when_to_post_id', '=', '3')
        ->orderBy('posts.created_at', 'DESC')
        ->get();
        
        if (isset($post)) {
            $message = "Saved Post";
            $status_code = SUCCESSCODE;
            $data = $post;
        }
        return response([
             'data' => $data,
             'message' => $message,
             'status_code' => $status_code
         ], $status_code);
    }

    public function handle()
    {
        $data= Post::where('when_to_post_id', '2')
       ->where('time', '<', date("H:i:s"))
       ->whereDate('date', '<=', date("Y-m-d"))
       ->get();
  
        if (!empty($data)) {
            $update['when_to_post_id'] = '1';
            DB::table('posts')->where('time', '<', date("H:i:s"))->whereDate('date', '<=', date("Y-m-d"))->update($update);
            $message = "Post Updated";
            $status_code = SUCCESSCODE;
        }

        return response([
            'data' => [],
            'message' => $message,
            'status_code' => $status_code
        ], $status_code);
    }

    public function artist_publish_list(Request $request, $artist_id)
    {
        $data = [];
        $message = "Failed to fetch artist post";
        $status_code = BADREQUEST;

        $post = Post::select(
            'posts.id',
            'posts.title',
            'posts.content',
            'posts.image',
            'posts.video',
            'posts.audio',
            'posts.live_stream',
            'posts.when_to_post_id',
            'posts.user_id',
            'posts.who_can_see_post_id',
            'posts.post_type_id',
            'posts.time',
            'posts.date',
            'posts.deleted_at',
            'users.id',
            'users.name',
            'users.username',
            'users.profile_pic',
            'posts.created_at',
        )->leftJoin('users', 'users.id', '=', 'posts.user_id')
        ->where('posts.when_to_post_id', '=', '1')
        ->where('posts.user_id', $artist_id)
        ->orderBy('posts.created_at', 'DESC')->get();
 
        if (isset($post)) {
            $message = "Artist Post";
            $status_code = SUCCESSCODE;
            $data = $post;
        }
        return response([
             'data' => $data,
             'message' => $message,
             'status_code' => $status_code
         ], $status_code);
    }

    public function feed(Request $request)
    {
        $data = [];
        $message = "Failed to fetch feed details";
        $status_code = BADREQUEST;

        $current_user = get_user();

        $users= Post::select(
            'users.name',
            'users.username',
            'users.profile_pic',
            'posts.id',
            'posts.title',
            'posts.content',
            'posts.image',
            'posts.video',
            'posts.audio',
            'posts.live_stream',
            'posts.when_to_post_id',
            'posts.user_id as post_user_id',
            'posts.who_can_see_post_id',
            'posts.post_type_id',
            'posts.time',
            'posts.date',
            'posts.created_at',
            'posts.deleted_at',
            'user_subscriptions.user_id'
        )
                    ->leftJoin('subscriptions', 'subscriptions.user_id', '=', 'posts.user_id')
                    ->leftJoin('user_subscriptions', 'subscriptions.id', '=', 'user_subscriptions.subscribe_id')
                    ->leftJoin('users', 'subscriptions.user_id', '=', 'users.id')
                    ->where('user_subscriptions.user_id', $current_user->id)
                    ->where('user_subscriptions.deleted_at', null)
                    ->where('user_subscriptions.status', 1)
                    ->get();
        $message = "User Feed";
        $status_code = SUCCESSCODE;
     
        return response([
            'data' => $users,
            'message' => $message,
            'status_code' => $status_code,
        ], $status_code);
    }
}
