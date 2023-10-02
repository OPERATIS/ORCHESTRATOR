<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Metric extends Model
{
    use HasFactory;

    public const METRCIS = ['l', 'c', 'p', 'q', 'ltv'];

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
