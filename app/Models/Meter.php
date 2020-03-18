<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Class Meter
 */
class Meter extends Model
{
    protected $table = 'meters';

    public $timestamps = true;

    protected $fillable = [
        'parent_id',
        'child_path',
        'parent_path',
        'user_id',
        'nst_lvl',
        'type_id',
        'status_id',
        'value',
        'ip_address',
        'login',
        'password',
        'serial',
        'contract',
        'rate_id',
        'operator_id',
        'address_id',
        'description',
        'request_pended',
        'is_replacement',
        'is_inquired',
        'not_response_cnt',
        'short_deferred_cnt',
        'long_deferred_cnt',
        'deferred_time',
        'display',
        'tozelesh'
    ];

    protected $guarded = [];

    public function rate(){
        return $this->belongsTo('App\Models\Rate');
    }

    public function status(){
        return $this->belongsTo('App\Models\MeterStatus');
    }

    public function address(){
        return $this->belongsTo('App\Models\Address');
    }

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function operator(){
        return $this->belongsTo('App\Models\MobileOperator');
    }

    public function scopeJoinAddress($query){
        return $query->join('addresses as a', 'a.id', '=', 'meters.address_id')->select('meters.*');
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