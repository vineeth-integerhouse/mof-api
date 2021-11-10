<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Exception;
use Illuminate\Support\Facades\DB;

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
            break;

        case 2:
            $post_data['image'] = $request->image;
            $post_data['content'] = $request->content;
            break;

        case 3:
            $post_data['video'] = $request->video;
            $post_data['content'] = $request->content;
            break;
            
        case 4:
            $post_data['audio'] = $request->audio;
            $post_data['content'] = $request->content;
            break;
            
        case 5:
            $post_data['live_stream'] = $request->live_stream;
            $post_data['content'] = $request->content;
            break;
        }
      
        $inserted_data = Post::create($post_data);

        if (!empty($post)) {
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
                'post_type_id'
            )->where('id', $inserted_data->id)->get()->first();
            ;
            $status_code = SUCCESSCODE;
            $message = __('user.post_success');
        }
        
        return response([
            'data' => $data,
            'message' => $message,
            'status_code' => $status_code,
        ], $status_code);
    }

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
                'post_type_id'
            )->where('id', $post_id)->get()->first();
        }
    
        return response([
        'data'        => $data,
        'message'     => $message,
        'status_code' => $status_code
          ], $status_code);
    }

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

    public function get(Request $request, $post_id)
    {
        $message =  __('user.post_fetch failed');
        $status_code = BADREQUEST;

        $data= Post::withTrashed()->select(
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
            'deleted_at',
        )->where('id', $post_id)->get()->first();

        if (isset($data)) {
            $message = __('user.post_fetch_success');
            $status_code = SUCCESSCODE;
        }

        return response([
            'data'        => $data,
            'message'     => $message,
            'status_code' => $status_code
        ], $status_code);
    }

     /*  Listing Post */
     public function list(Request $request)
     {
         $data = [];
         $message = __('user.post_fetch failed');
         $status_code = BADREQUEST;
 
 
         $limit = !empty($request->input('limit')) ? $request->input('limit') : 10;
         $sort_column = !empty($request->input('sort_column')) ? $request->input('sort_column') : "created_at";
         $sort_direction = !empty($request->input('sort_direction')) ? $request->input('sort_direction')  : "desc";
 
         $page = (!empty($request->input('page')) && $request->input('page') > 0) ? intval($request->input('page')) : 1;
         $offset = ($page > 1) ? ($limit * ($page - 1)) : 0;
 
         $post = Post::
         select(
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
         )->orderBy(DB::raw('posts.'.$sort_column), $sort_direction)->paginate($limit, $offset);
 
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
 
}
