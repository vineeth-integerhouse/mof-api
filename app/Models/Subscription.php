<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    public $fillable = ['user_id','subscription_type_id', 'price'];

    public function usersubscription()
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }

}
