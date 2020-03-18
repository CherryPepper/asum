<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 */
class Role extends Model
{
    protected $table = 'roles';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'slug'
    ];

    protected $guarded = [];


}