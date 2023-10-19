<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * @return BelongsTo
     */
    public function alert(): BelongsTo
    {
        return $this->belongsTo(Alert::class);
    }

    /**
     * @param Builder $query
     * @param $userId
     * @return Builder
     */
    public function scopeUser(Builder $query, $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}
