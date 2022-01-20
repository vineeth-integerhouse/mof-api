<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfileView extends Model
{
    use HasFactory,SoftDeletes;

    public $fillable = ['user_id','profile_view'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
