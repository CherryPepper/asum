<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Rate
 */
class Rate extends Model
{
    protected $table = 'rates';

    public $timestamps = true;

    protected $fillable = [
        'type',
        'title',
        'description',
        'display'
    ];

    protected $guarded = [];


    public function intervals(){
        return $this->hasMany('App\Models\RateInterval');
    }
}