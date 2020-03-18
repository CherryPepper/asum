<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MeterStatus
 */
class MeterReplacement extends Model
{
    protected $table = 'meter_replacements';

    public $timestamps = false;

    protected $fillable = [
        'meter_id',
        'value_old',
        'value_new',
        'difference',
        'created_at'
    ];

    protected $guarded = [];


}