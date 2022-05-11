<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Like;
use App\Models\ProfileView;
use App\Models\ReplyComment;
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

        $limit = !empty($request->input('limit')) ? $request->input('limit') : 10;
  
        $page = (!empty($request->input('page')) && $request->input('page') > 0) ? intval($request->input('page')) : 1;
        $offset = ($page > 1) ? ($limit * ($page - 1)) : 0;
 
        $data= Comment::select(
            'comments.id',
            'comments.post_id',
            'comments.user_id',
            'comments.comment',
            'users.username',
            'users.profile_pic',
            'comments.created_at'
        )->leftJoin('users', 'users.id', '=', 'comments.user_id')
        ->where('comments.post_id', $post_id)
        ->orderBy('comments.created_at', 'DESC')
        ->paginate($limit, $offset);
     
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
             'like'=> $request->like,
           ]
        );

        $like= Like::select(
            'id',
            'post_id',
            'user_id',
            'like'
        )->where('id',$inserted_data->id)->first();

        $message = __('user.add_like_success');
        $status_code = SUCCESSCODE;
          
        return response([
            'data' => $like,
            'message' => $message,
            'status_code' => $status_code,
        ], $status_code);
    }

    public function profile_views(Request $request)
    {
        $data = [];
       
        $message = __('user.add_like_failed');
        $status_code = BADREQUEST;
 
        $current_user = get_user();
   
        $inserted_data = ProfileView::create(
            [
                'user_id'=> $request->artist_id,
         
             'profile_view'=> DB::raw('profile_view+1'),
           ]
        );
        
        $profile_views= ProfileView::select(
            'id',
            'user_id',
            'profile_view'
        )->where('id', $inserted_data->id)->get()->first();
        $message = __('user.add_like_success');
        $status_code = SUCCESSCODE;
          
        return response([
            'data' => $profile_views,
            'message' => $message,
            'status_code' => $status_code,
        ], $status_code);
    }

    public function reply(Request $request, $comment_id)
    {
        $data = [];
       
        $message = __('user.add_comment_failed');
        $status_code = BADREQUEST;
 
        $current_user = get_user();
   
        $data['post_id'] = $request->post_id;
        $data['comment_id'] = $comment_id;
        $data['user_id'] = $current_user->id;
        $data['comment'] = $request->comment;
        
            
        $inserted_data = ReplyComment::create($data);
 
        $reply= ReplyComment::select(
            'id',
            'post_id',
            'comment_id',
            'user_id',
            'comment'
        )->where('id', $inserted_data->id)->get()->first();
        $message = __('user.add_comment_success');
        $status_code = SUCCESSCODE;
          
        return response([
            'data' => $reply,
            'message' => $message,
            'status_code' => $status_code,
        ], $status_code);
    }

    public function reply_fetch(Request $request, $comment_id)
    {
        $message =  __('user.comment_fetch failed');
        $status_code = BADREQUEST;

        $limit = !empty($request->input('limit')) ? $request->input('limit') : 10;
  
        $page = (!empty($request->input('page')) && $request->input('page') > 0) ? intval($request->input('page')) : 1;
        $offset = ($page > 1) ? ($limit * ($page - 1)) : 0;
 
        $data= ReplyComment::select(
            'reply_comments.id',
            'reply_comments.post_id',
            'reply_comments.comment_id',
            'reply_comments.user_id',
            'reply_comments.comment',
            'users.username',
            'users.profile_pic',
            'reply_comments.created_at'
        )->leftJoin('users', 'users.id', '=', 'reply_comments.user_id')
        ->where('reply_comments.comment_id', $comment_id)
        ->orderBy('reply_comments.created_at', 'DESC')
        ->paginate($limit, $offset);
     
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

    public function like_fetch(Request $request, $post_id, $user_id)
    {
        $message =  "No like";
        $status_code = SUCCESSCODE;
        
        $data= like::select(
            'id',
            'post_id',
            'user_id',
            'like',
        )
        ->where('post_id', $post_id)
        ->where('user_id', $user_id)
        ->orderBy('created_at', 'DESC')
        ->first();
     
        if (isset($data)) {
            $message = "Post likes";
            $status_code = SUCCESSCODE;
        }
 
        return response([
             'data'        => $data,
             'message'     => $message,
             'status_code' => $status_code
         ], $status_code);
    }
}
