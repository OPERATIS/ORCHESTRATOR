<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analysis extends Model
{
    use HasFactory;

    public const PERIOD_60_HOURS = '60_hours';
    public const PERIOD_30_DAYS = '30_days';

    protected $table = 'analyzes';
    protected $guarded = [];
    public $dates = [
        'start_period',
        'end_period'
    ];
}
