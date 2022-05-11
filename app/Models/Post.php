<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use phpDocumentor\Reflection\DocBlock\Tag;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    public $fillable = ['user_id','title', 'image', 'video', 'audio','live_stream', 'who_can_see_post_id', 'when_to_post_id', 'post_type_id', 'content','date','time'];

    public function comment()
    {
        return $this->hasMany(Comment::class);
    }

    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }

    public function postTag()
    {
        return $this->hasMany(PostTag::class,'post_id');
    }

    
    public function like()
    {
        return $this->hasMany(Like::class);
    }
}

