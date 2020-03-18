<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OControlValue extends Model
{
    protected $table = 'o_control_values';

    public $timestamps = false;

    protected $fillable = [
        'type_id',
        'difference',
        'accruals',
        'created_at'
    ];

    public function type(){
        return $this->hasOne('App\Models\OMeter', 'id', 'type_id');
    }
}
