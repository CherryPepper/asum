<?php

namespace App\Models\Tozelesh;

use Illuminate\Database\Eloquent\Model;

class Lamp extends Model
{
    protected $table = 'tz_object_lamps';

    public $timestamps = false;

    protected $fillable = [
        'object_id',
        'lamp_id',
        'coordinates',
        'display',
        'created_at'
    ];

    public function type(){
        return $this->belongsTo('App\Models\Tozelesh\LampType', 'lamp_id');
    }
}
