<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RateInterval
 */
class RateInterval extends Model
{
    protected $table = 'rate_intervals';

    public $timestamps = false;

    protected $fillable = [
        'rate_id',
        'time_start',
        'time_end',
        'price',
        'options'
    ];

    protected $guarded = [];

        
}