<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserRegionsAccess
 */
class UserRegionsAccess extends Model
{
    protected $table = 'user_regions_access';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'address_id'
    ];

    protected $guarded = [];

    public function address(){
        return $this->belongsTo('App\Models\Address');
    }
        
}