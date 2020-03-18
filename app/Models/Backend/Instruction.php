<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;

class Instruction extends Model
{
    protected $table = 'backend_instructions';

    public $timestamps = false;

    protected $fillable = [
        'parent_id',
        'meter_id',
        'action_id',
        'status',
        'created_at',
        'completed_at',
        'code_error',
        'priority',
    ];

    public function action(){
        return $this->belongsTo('App\Models\Backend\Action');
    }

    public function meter(){
        return $this->belongsTo('App\Models\Meter');
    }

    public function i_query(){
        return $this->HasOne('App\Models\Backend\Query');
    }
}
