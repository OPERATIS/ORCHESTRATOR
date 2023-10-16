<?php

namespace App\Models;

use App\Services\Notifications;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    public const INCREASED = 'Increased';
    public const DECREASED = 'Decreased';
    public const UNCHANGED = 'Unchanged';

    public const RESULT_FOR_NOTIFICATIONS = [
        self::INCREASED,
        self::DECREASED
    ];

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
        $query->where('period', Metric::PERIOD_HOUR)
            ->whereIn('result', self::RESULT_FOR_NOTIFICATIONS);
    }

    /**
     * @param Builder $query
     */
    public function scopeForRecommendations(Builder $query)
    {
        $query->where('period', Metric::PERIOD_DAY);
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return Notifications::getMessageFromAlert($this);
    }
}
