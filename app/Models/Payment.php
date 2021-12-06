<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;
    public $fillable = ['name', 'payment_date', 'amount', 'status', 'payment_method', 'stripe_reference_number', 'payer','payee','card_detail_id'];
}
