<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Metric extends Model
{
    use HasFactory;

    public const PERIOD_HOUR = '1_hour';
    public const PERIOD_DAY = '1_day';
    public const METRICS = ['l', 'c', 'p', 'q', 'ltv'];

    protected $guarded = [];
    public $dates = [
        'start_period',
        'end_period'
    ];
    protected $casts = [
        'l' => 'float',
        'c' => 'float',
        'p' => 'float',
        'q' => 'float',
        'ltv' => 'float',
    ];
}
