<?php
namespace App\Helpers;

use App\Models\MonthAssoc;
use Carbon\Carbon;

class DateTime
{
    public static function getIntervalHour($interval){
        $data['hour_start'] = $interval->time_start / 60;
        $data['hour_start'] = ($data['hour_start'] < 10) ? '0'.$data['hour_start'].':00' : $data['hour_start'].':00';

        $data['hour_end'] = $interval->time_end / 60;
        $data['hour_end'] = ($data['hour_end'] < 10) ? '0'.$data['hour_end'].':00' : $data['hour_end'].':00';

        return $data;
    }

    public static function getAllMonths(){
        $months = MonthAssoc::all();
        $months_arr = [];

        foreach ($months as $month)
            $months_arr[$month->num] = $month;

        return $months_arr;
    }

    public static function getMonthByNum($date){
        $months_arr = self::getAllMonths();

        return $months_arr[Carbon::parse($date)->month]->title;
    }

    public static function getDateForConsumptionReport($frame, $date, $months_arr){
        switch ($frame){
            case 1:{
                $date = $months_arr[Carbon::parse($date)->month]->title;
                break;
            }
            case 2:{
                $date = Carbon::parse($date)->format('d.m.Y');
                break;
            }
            case 3:{
                $date = Carbon::parse($date)->format('H:00');
                break;
            }
            case 4:{
                $date = Carbon::parse($date)->format('H:i');
                break;
            }
        }

        return $date;
    }
}