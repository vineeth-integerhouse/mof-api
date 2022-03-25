<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSubscription extends Model
{
    use HasFactory, SoftDeletes;

    public $fillable = ['user_id', 'subscribe_id',  'promotion_id', 'status'];

    public function subscriptions()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
