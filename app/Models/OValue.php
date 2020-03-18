<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class OValue extends Model
{
    protected $table = 'o_values';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'type_id',
        'meter_id',
        'value',
        'difference',
        'accruals',
        'manually',
        'created_at',
    ];

    public function meter(){
        return $this->hasOne('App\Models\OUserMeter', 'id', 'meter_id');
    }

    public function user(){
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public static function addValue($request){
        $current_date = self::getOmetersCurrentDate();
        $difference = 0;

        $prev_value = self::where('type_id', $request->type_id)
            ->where('user_id', $request->user_id)
            ->where('created_at', '<', $current_date)
            ->orderBy('created_at', 'desc')
            ->first();

        if($prev_value->value > $request->value)
            return [
                'status' => 'error',
                'message' => 'Текущее показание не может быть меньше предыдущего'
            ];

        $type = OMeter::where('id', $request->type_id)->first();

        $current_meter = OUserMeter::where('type_id', $request->type_id)
            ->where('user_id', $request->user_id)->first();

        if(!empty($prev_value))
            $difference = $request->value-$prev_value->value;

        self::updateOrCreate([
            'user_id' => $request->user_id,
            'created_at' => $current_date,
            'type_id' => $request->type_id
        ], [
            'meter_id' => $current_meter->id,
            'user_id' => $request->user_id,
            'created_at' => $current_date,
            'type_id' => $request->type_id,
            'difference' => $difference,
            'value' => $request->value,
            'accruals' => $difference*$type->price,
            'manually' => 1
        ]);
    }

    public static function calculateValues($type_id){
        $current_date = self::getOmetersCurrentDate();

        /** Get current control value */
        $current_value = OControlValue::with(['type'])->where('type_id', $type_id)
            ->where('created_at', $current_date)->first();

        if(!empty($current_value)){
            /** Get meters with manually values **/
            $with_values = self::where('type_id', $type_id)
                ->where('manually', 1)->where('display', 1)
                ->where('created_at', $current_date)->get();

            $with_values_sum = 0;
            $with_values_arr = [];

            foreach ($with_values as $vl){
                $with_values_sum += $vl->difference;
                $with_values_arr[] = $vl->meter_id;
            }

            /** Get meters without manually values */
            $without_values = OUserMeter::whereNotIn('id', $with_values_arr)->where('display', 1)
                ->where('type_id', $type_id)->get();

            if(sizeof($without_values) > 0){
                /** Calculate avg difference for meters */
                $avg_difference = ($current_value->difference-$with_values_sum)/sizeof($without_values);

                /** Set avg values for meters */
                foreach ($without_values as $wvl){
                    /** Get last value for this meter */
                    $last_val = self::where('user_id', $wvl->user_id)
                        ->where('type_id', $wvl->type_id)->orderBy('id', 'desc')->first();

                    if(!empty($last_val)){
                        if($last_val->created_at == $current_date){
                            $last_val->value -= $last_val->difference;
                        }
                        else
                            $last_val->created_at = $current_date;

                        $last_val->manually = 0;
                        $last_val->difference = $avg_difference;
                        $last_val->value += $avg_difference;
                        $last_val->accruals = $current_value->type->price*$avg_difference;
                    }

                    self::updateOrCreate([
                        'type_id' => $wvl->type_id,
                        'user_id' => $wvl->user_id,
                        'created_at' => $current_date
                    ], $last_val->attributes);
                }

                return true;
            }
            else
                return false;
        }
        else
            return false;
    }

    public static function getOmetersCurrentDate(){
        $current_date = Carbon::now();

        if($current_date->day < 20)
            $current_date = Carbon::parse($current_date->subMonth())->format('Y-m-20 00:00:00');
        else
            $current_date = Carbon::parse($current_date)->format('Y-m-20 00:00:00');

        return $current_date;
    }
}
