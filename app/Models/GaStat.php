<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GaStat extends Model
{
    use HasFactory;

    public const DEMO_UNIQUE_TABLE_ID = 401;
    public const DEMO_INTEGRATION_ID = 4;

    protected $guarded = [];
    public $dates = [
        'start_period',
        'end_period'
    ];
}
