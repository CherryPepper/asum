<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MeterStatus
 */
class MeterError extends Model
{
    protected $table = 'meter_errors';

    public $timestamps = false;

    protected $fillable = [
        'meter_id',
        'error_code',
        'note',
        'created_at'
    ];

    protected $guarded = [];


}