<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Address
 */
class Address extends Model
{
    protected $table = 'addresses';

    public $timestamps = true;

    protected $fillable = [
        'parent_id',
        'nst_lvl',
        'name',
        'hash',
        'display'
    ];

    protected $guarded = [];


    public function parent()
    {
        return $this->belongsTo('App\Models\Address','parent_id');
    }

    public function parentRecursive()
    {
        return $this->parent()->with('parentRecursive');
    }
}