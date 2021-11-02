<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    public $fillable = ['user_id','title', 'image', 'video', 'audio','live_stream', 'who_can_see_post_id', 'when_to_post_id'];
}
