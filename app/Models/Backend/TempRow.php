<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;

class TempRow extends Model
{
    protected $table = 'backend_temp_rows';

    public $timestamps = false;

    protected $fillable = [
        'point',
        'meter_id'
    ];

    public function meter(){
        return $this->belongsTo('App\Models\Meter');
    }
}
