<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkout extends Model
{
    use HasFactory;

    protected $guarded = [];
    public $dates = [
        'checkout_created_at',
        'checkout_completed_at'
    ];
    public $casts = [
        'gift_cards' => 'json',
    ];
}
