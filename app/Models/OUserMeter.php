<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OUserMeter extends Model
{
    protected $table = 'o_users_meters';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'type_id',
        'serial',
        'created_at'
    ];

    public function type(){
        return $this->hasOne('App\Models\OMeter', 'id', 'type_id');
    }
}
