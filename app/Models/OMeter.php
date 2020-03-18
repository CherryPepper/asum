<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OMeter extends Model
{
    protected $table = 'o_meters';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'name_en',
        'price'
    ];
}
