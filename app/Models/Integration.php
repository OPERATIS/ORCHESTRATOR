<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Integration extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    /**
     * @param Builder $query
     */
    public function scopeIgnoreDemo(Builder $query)
    {
        $query->where('user_id', '!=', User::DEMO_ID);
    }

    /**
     * @param Builder $query
     */
    public function scopeActual(Builder $query)
    {
        $query->where('actual', true);
    }
}
