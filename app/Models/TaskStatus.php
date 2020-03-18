<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TaskStatus
 */
class TaskStatus extends Model
{
    protected $table = 'task_status';

    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

    protected $guarded = [];

        
}