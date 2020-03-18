<?php

namespace App\Models\Tozelesh;

use App\Models\Meter;
use App\Models\Tozelesh\Lamp;
use Illuminate\Database\Eloquent\Model;

class TzObject extends Model
{
    protected $table = 'tz_object';

    public $timestamps = false;

    protected $fillable = [
        'meter_id',
        'time_on',
        'time_off',
        'coordinates',
        'display',
        'created_at'
    ];

    public function meter(){
        return $this->belongsTo('App\Models\Meter');
    }

    public function lamps(){
        return $this->hasMany('App\Models\Tozelesh\Lamp', 'object_id');
    }

    public static function deleteObject($id){
        $object = TzObject::where('id', $id)->first();

        self::where('id', $id)->delete();
        Meter::where('id', $object->meter_id)->delete();
        Lamp::where('object_id', $object->id)->delete();

        return true;
    }
}
