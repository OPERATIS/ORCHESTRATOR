<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public const DEMO_CONNECT_ID = 6;

    protected $guarded = [];
    public $dates = [
        'order_created_at'
    ];
}
