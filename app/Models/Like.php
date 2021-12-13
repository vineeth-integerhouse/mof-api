<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Like extends Model
{
    use HasFactory,SoftDeletes;

    public $fillable = ['user_id','post_id', 'likes'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
