<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Metric extends Model
{
    use HasFactory;

    public const PERIOD_HOUR = '1_hour';
    public const PERIOD_DAY = '1_day';

    public const METRICS = ['l', 'c', 'p', 'q', 'ltv'];

    public const L = 'l';
    public const C = 'c';
    public const P = 'p';
    public const Q = 'q';
    public const LTV = 'ltv';

    public const NAMES = [
        self::L => 'Leads',
        self::C => 'Conversion Rate',
        self::P => 'Average Check',
        self::Q => 'Average Quantity of Orders per Client',
        self::LTV => 'Average lifetime value per successful client',
    ];

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
        'map' => 'json',
        'pmd' => 'json'
    ];

    /**
     * @param Builder $query
     * @param $period
     */
    public function scopePeriod(Builder $query, $period)
    {
        $query->where('period', $period);
    }
}
