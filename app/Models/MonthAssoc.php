<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MeterStatus
 */
class MonthAssoc extends Model
{
    protected $table = 'assoc_month';

    public $timestamps = false;

    protected $fillable = [
        'num',
        'title'
    ];

    protected $guarded = [];


}