<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public const DEMO_INTEGRATION_ID = 6;

    protected $guarded = [];
    public $dates = [
        'order_created_at',
        'canceled_at'
    ];
    public $casts = [
        'discount_codes' => 'json',
        'payment_gateway_names' => 'json',
    ];
}
