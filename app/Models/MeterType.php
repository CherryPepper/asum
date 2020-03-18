<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MeterType
 */
class MeterType extends Model
{
    protected $table = 'meter_types';

    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

    protected $guarded = [];

        
}