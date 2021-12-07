<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CardDetail extends Model
{
    use HasFactory,SoftDeletes;
    public $fillable = ['user_id','name_on_card', 'credit_card_number','country_region','zip_code'];
}
