<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    /* Add Comment*/
    public function add(Request $request)
    {
        $data = [];
       
        $message = __('user.add_comment_failed');
        $status_code = BADREQUEST;
 
        $current_user = get_user();
   
        $data['post_id'] = $request->post_id;
        $data['user_id'] = $current_user->id;
        $data['comment'] = $request->comment;
        
            
        $inserted_data = Comment::create($data);
 
        $comment= Comment::select(
            'id',
            'post_id',
            'user_id',
            'comment'
        )->where('id', $inserted_data->id)->get()->first();
        $message = __('user.add_comment_success');
        $status_code = SUCCESSCODE;
          
        return response([
            'data' => $comment,
            'message' => $message,
            'status_code' => $status_code,
        ], $status_code);
    }

    /* Fetch Comment*/
    public function fetch(Request $request, $post_id)
    {
        $message =  __('user.comment_fetch failed');
        $status_code = BADREQUEST;
 
        $data= Comment::select(
            'id',
            'post_id',
            'user_id',
            'comment'
        )->where('post_id', $post_id)->get();
 
        if (isset($data)) {
            $message = __('user.comment_fetch_success');
            $status_code = SUCCESSCODE;
        }
 
        return response([
             'data'        => $data,
             'message'     => $message,
             'status_code' => $status_code
         ], $status_code);
    }

    /* Delete Comment*/
    public function delete(Request $request, $comment_id)
    {
        $data      = [];
        $message =  __('user.invalid_user');
        $status_code = BADREQUEST;
  
        $current_user = get_user();
  
        $comment = Comment::where('id', $comment_id)->first();
  
        $comment_data = Comment::where('id', $comment_id)->where('user_id', $current_user->id)->delete();
         
        if ($comment_data === 1) {
            $data['id']   = $comment->id;
            $message = __('user.comment_delete_success');
            $status_code = SUCCESSCODE;
        } else {
            $message = __('user.comment_delete_failed');
            $status_code = BADREQUEST;
        }
      
        return response([
              'data'        => $data,
              'message'     => $message,
              'status_code' => $status_code
          ], $status_code);
    }


    /* Add Like*/
    public function add_like(Request $request)
    {
        $data = [];
       
        $message = __('user.add_like_failed');
        $status_code = BADREQUEST;
 
        $current_user = get_user();
   
        $inserted_data = Like::updateOrCreate(
            [
                'user_id'=> $current_user->id,
                'post_id'=>$request->post_id,
            ],
            [
           
             'likes'=> DB::raw('likes+1'),
           ]
        );
        
        $like= Like::select(
            'id',
            'post_id',
            'user_id',
            'likes'
        )->where('id', $inserted_data->id)->get()->first();
        $message = __('user.add_like_success');
        $status_code = SUCCESSCODE;
          
        return response([
            'data' => $like,
            'message' => $message,
            'status_code' => $status_code,
        ], $status_code);
    }
}
