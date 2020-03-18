<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;

class Procedure extends Model
{
    protected $table = 'backend_procedures';

    public $timestamps = false;

    protected $fillable = [
        'method',
        'complete_time',
        'time_limit',
        'count_complete',
        'enabled'
    ];
}
