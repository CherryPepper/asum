<?php

namespace App\Models\Tozelesh;

use Illuminate\Database\Eloquent\Model;

class LampType extends Model
{
    protected $table = 'tz_object_lamps_types';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'consumption',
        'img',
        'display',
        'created_at'
    ];

    public function object(){
        return $this->hasOne('App\Models\Tozelesh\TzObject');
    }
}
