<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileOperator extends Model
{
    protected $table = 'mobile_operators';

    protected $fillable = [
        'name',
        'name_en',
        'created_at'
    ];
}
