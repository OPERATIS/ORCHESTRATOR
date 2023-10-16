<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Integration extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @param Builder $query
     */
    public function scopeIgnoreDemo(Builder $query)
    {
        $query->where('user_id', '!=', User::DEMO_ID);
    }
}
