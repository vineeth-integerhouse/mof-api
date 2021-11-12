<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialProfile extends Model
{
    use HasFactory, SoftDeletes;
    public $fillable = ['user_id','social_profile'];
}
