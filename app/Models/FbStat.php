<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FbStat extends Model
{
    use HasFactory;

    public const DEMO_AD_ID = 501;
    public const DEMO_INTEGRATION_ID = 5;

    protected $guarded = [];
    public $dates = [
        'start_period',
        'end_period'
    ];
}
