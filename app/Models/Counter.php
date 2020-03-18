<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    protected $table = 'counter';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'value',
        'date'
    ];

    public static function getNum($name){
        $counter = self::where('name', $name)->first();

        if(Carbon::parse($counter->date) < Carbon::today()){
            $counter->date = Carbon::today();
            $counter->value = '001';
            $counter->save();
        }

        $value = $counter->value;
        $counter->value = str_pad((int)++$counter->value, 3, '0', STR_PAD_LEFT);
        $counter->save();

        return $value;
    }
}
