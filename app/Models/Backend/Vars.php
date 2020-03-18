<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Rate
 */
class Vars extends Model
{
    protected $table = 'backend_vars';

    public $timestamps = false;

    protected $fillable = [
        'key',
        'value',
        'notice',
        'is_condition',
    ];

    protected $guarded = [];
}