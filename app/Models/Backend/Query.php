<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;

class Query extends Model
{
    protected $table = 'backend_queries';

    public $timestamps = false;

    protected $fillable = [
        'meter_id',
        'action_id',
        'request',
        'response',
        'instruction_id',
        'code_error',
        'created_at',
        'completed_at',
    ];

    public function instruction(){
        return $this->belongsTo('App\Models\Backend\Instruction');
    }

    public function meter(){
        return $this->belongsTo('App\Models\Meter');
    }

    public function action(){
        return $this->belongsTo('App\Models\Backend\Action');
    }
}
