<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Class Task
 */
class Task extends Model
{
    protected $table = 'tasks';

    public $timestamps = true;

    protected $fillable = [
        'meter_id',
        'address_id',
        'from_user_id',
        'employer_id',
        'role_id',
        'date_start',
        'date_end',
        'date_complete',
        'message',
        'code_error'
    ];

    protected $guarded = [];


    public function address(){
        return $this->belongsTo('App\Models\Address');
    }

    public function employer(){
        return $this->belongsTo('App\User', 'employer_id');
    }

    public function scopeCheckAccess($query){
        if(Auth::user()->role->slug != 'administrator'){
            $allowed_str = '';
            $allowed = Auth::user()->access_regions()->with('address')->get();

            foreach ($allowed as $alw)
                $allowed_str .= $alw->address->path_childs.',';
            $allowed_str = rtrim($allowed_str, ',');

            return $query->whereIn("address_id", explode(',', $allowed_str));
        }
    }
}