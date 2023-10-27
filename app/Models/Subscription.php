<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stripe\Subscription as StripeSubscription;

class Subscription extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where(function ($query) {
            $query->where('user_subscribe_to', '>', Carbon::now())
                ->orWhereNull('user_subscribe_to');
        })
            ->where(function ($query) {
                $query->whereIn('status', [StripeSubscription::STATUS_ACTIVE, StripeSubscription::STATUS_CANCELED]);
            });
    }

    public function scopeProblem($query)
    {
        return $query->where(function ($query) {
            $query->where('user_subscribe_to', '>', Carbon::now())
                ->orWhereNull('user_subscribe_to');
        })
            ->whereNotIn('status', [StripeSubscription::STATUS_ACTIVE, StripeSubscription::STATUS_CANCELED]);
    }

    public function getIsProblem(): bool
    {
        return !in_array($this->status, [StripeSubscription::STATUS_ACTIVE, StripeSubscription::STATUS_CANCELED]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
