<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MeterStatus
 */
class MeterStatus extends Model
{
    protected $table = 'meter_status';

    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

    protected $guarded = [];

        
}