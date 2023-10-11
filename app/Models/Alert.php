<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    protected $guarded = [];
    public $dates = [
        'start_period',
        'end_period'
    ];

    /**
     * @param Builder $query
     */
    public function scopeForNotifications(Builder $query)
    {
        $query->where('period', Metric::PERIOD_HOUR);
    }

    /**
     * @param Builder $query
     */
    public function scopeForRecommendations(Builder $query)
    {
        $query->where('period', Metric::PERIOD_DAY);
    }
}
