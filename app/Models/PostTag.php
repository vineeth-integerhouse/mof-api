<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostTag extends Model
{
    use HasFactory,SoftDeletes;

    public $fillable = ['user_id','post_id','tagged_user_id'];

    public function post()
    {
        return $this->belongsTo(Post::class, 'tag_post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function taggedUser()
    {
        return $this->belongsTo(User::class, 'tagged_user_id');
    }
}
