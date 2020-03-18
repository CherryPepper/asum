<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Rate
 */
class Action extends Model
{
    protected $table = 'backend_actions';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    protected $guarded = [];
}