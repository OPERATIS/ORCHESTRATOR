<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analysis extends Model
{
    use HasFactory;

    protected $table = 'analyzes';
    protected $guarded = [];
    public $dates = [
        'start_period',
        'end_period'
    ];
}
