<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AggregationFbStat extends Model
{
    use HasFactory;

    protected $guarded = [];
    public $dates = [
        'period'
    ];
}
