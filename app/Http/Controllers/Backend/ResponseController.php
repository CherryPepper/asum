<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\Addresses;
use App\Models\Backend\Query;
use App\Models\Backend\TempRow;
use App\Models\MeterReplacement;
use App\Models\MetersValue;
use App\Models\MobileOperator;
use App\Models\Backend\Instruction;
use App\Models\Meter;
use App\Models\Notification;
use App\Models\RateInterval;
use Carbon\Carbon;

class ResponseController extends Controller
{
    public function __construct($request){
        parent::__construct($request);

        /** Response from meter */
        if($this->query_id > 0){
            $query = Query::where('id', $this->query_id)->first();

            if(!empty($query)){
                /** If we get response from meter, but he did not respond before, set meter as active. */
                if(($query->meter->status_id == 2) && (($this->param == 'error' && $this->value == 1001) ? false : true)){
                    Meter::where('id', $query->meter->id)->update([
                        'not_response_cnt' => 0,
                        'deferred_time' => null,
                        'status_id' => 1
                    ]);
                }

                if($this->param != 'error'){
                    $this->operator_id = MobileOperator::where('name_en', $this->operator)->first()->id;

                    switch ($query->action->name){
                        case 'get_multi_info':{
                            if(!empty($this->pt)){
                                /** Parse value from meter */
                                list($date, $time, $value) = explode(',', $this->pt);
                                $point = Carbon::parse($date.' '.$time);
                                $value = round($value, 2);
                                $time_of_day = ($point->hour*60)+$point->minute;
                                $difference = 0;
                                $accruals = 0;

                                $meter = Meter::with('address.parentRecursive')
                                    ->where('id', $query->meter_id)->first();

                                /** Get rate interval for point */
                                $interval = RateInterval::where('rate_id', $meter->rate_id)
                                    ->where('time_end', '>=', $time_of_day)
                                    ->orderBy('time_end', 'ASC')
                                    ->first();
                                if(empty($interval)){
                                    $interval = RateInterval::where('rate_id', $meter->rate_id)
                                        ->where('time_start', '<=', $time_of_day)
                                        ->orderBy('time_start', 'DESC')
                                        ->first();
                                }
                                /** Get rate interval for point END */

                                /** Get prev time point for calculate difference */
                                $model = new MetersValue();
                                $model->setTable('meter_id_'.$meter->id);

                                $prev_point = $model->where('time_point', '<', $point->format('Y-m-d H:i:s'))
                                    ->whereNotNull('query_id')
                                    ->where('value', '>', 0)
                                    ->orderBy('time_point', 'DESC')
                                    ->first();

                                if(!empty($prev_point)){
                                    $difference = round($value - $prev_point->value, 2);
                                    $accruals = round($difference*$interval->price, 2);
                                }

                                /** If it's not missed request */
                                if($this->request != 'missed'){
                                    /** Update meter value */
                                    Meter::where('id', $meter->id)->update([
                                        'is_replacement' => 0,
                                        'value' => $value
                                    ]);

                                    if($meter->is_replacement == 1){
                                        MeterReplacement::insert([
                                            'meter_id' => $meter->id,
                                            'value_old' => $prev_point->value,
                                            'value_new' => $value,
                                            'difference' => $difference,
                                            'created_at' => Carbon::now()
                                        ]);

                                        $difference = 0;
                                        $accruals = 0;
                                    }
                                }else{
                                    /** Calculate next point */
                                    $next_point = $model->where('time_point', '>', $point->format('Y-m-d H:i:s'))
                                        ->whereNotNull('query_id')
                                        ->where('value', '>', 0)
                                        ->orderBy('time_point', 'ASC')
                                        ->first();

                                    if(!empty($next_point)){
                                        $diff = round($next_point->value - $value, 2);
                                        $acr = round($diff*$interval->price, 2);

                                        $next_point->setTable('meter_id_'.$meter->id);
                                        $next_point->update([
                                            'difference' => $diff,
                                            'accruals' => $acr
                                        ]);
                                    }
                                }

                                if(isset($prev_point->value) && ($prev_point->value > $value)){
                                    Notification::insert([
                                        'chunk' => 0,
                                        'user_id' => 1,
                                        'title' => "Возможный сбой счетчика - {$meter->serial}",
                                        'message' => "Последнее показание счетчика {$meter->serial} отличается от ожидаемых. 
                                                        <br> Адрес счетчика - ".Addresses::AdrString($meter->address)."
                                                        <br> Предыдущее показание - {$prev_point->value}
                                                        <br> Последнее показание - {$value}",
                                        'created_at' => Carbon::now(),
                                        'unread' => 1
                                    ]);
                                }

                                /** Update point value for meter */
                                $model->where('time_point', $point->format('Y-m-d H:i:s'))->update([
                                    'value' => $value,
                                    'difference' => $difference,
                                    'accruals' => $accruals,
                                    'query_id' => $query->id
                                ]);

                                /** Delete current point */
                                TempRow::where('point', $point->format('Y-m-d H:i:s'))
                                    ->where('meter_id', $meter->id)
                                    ->delete();

                                $this->value = $value;
                            }else{
                                echo 'Param pt is empty.';
                                die;
                            }
                            break;
                        }
                        case 'short_data':{
                            $point = $this->current_point;
                            $value = round($this->value, 2);
                            $time_of_day = ($point->hour*60)+$point->minute;
                            $difference = 0;
                            $accruals = 0;

                            $meter = Meter::where('id', $query->meter_id)->first();

                            /** Get rate interval for point */
                            $interval = RateInterval::where('rate_id', $meter->rate_id)
                                ->where('time_end', '>=', $time_of_day)
                                ->orderBy('time_end', 'ASC')
                                ->first();
                            if(empty($interval)){
                                $interval = RateInterval::where('rate_id', $meter->rate_id)
                                    ->where('time_start', '<=', $time_of_day)
                                    ->orderBy('time_start', 'DESC')
                                    ->first();
                            }
                            /** Get rate interval for point END */

                            /** Get prev time point for calculate difference */
                            $model = new MetersValue();
                            $model->setTable('meter_id_'.$meter->id);

                            $prev_point = $model->where('time_point', '<', $point->format('Y-m-d H:i:s'))
                                ->whereNotNull('query_id')
                                ->where('value', '>', 0)
                                ->orderBy('time_point', 'DESC')
                                ->first();

                            if(!empty($prev_point)){
                                $difference = round($value - $prev_point->value, 2);
                                $accruals = round($difference*$interval->price, 2);
                            }

                            Meter::where('id', $query->meter->id)->update([
                                'value' => $this->value
                            ]);

                            /** Update point value for meter */
                            $model->where('time_point', $point->format('Y-m-d H:i:s'))->update([
                                'value' => $value,
                                'difference' => $difference,
                                'accruals' => $accruals,
                                'query_id' => $query->id
                            ]);

                            /** Delete current point */
                            TempRow::where('point', $point->format('Y-m-d H:i:s'))
                                ->where('meter_id', $meter->id)
                                ->delete();

                            $this->value = $value;

                            break;
                        }
                        case 'get_serial_meter':{
                            break;
                        }
                        case 'meter_on':{
                            Meter::where('id', $query->meter->id)->update([
                                'status_id' => 1
                            ]);
                            break;
                        }
                        case 'meter_off':{
                            Meter::where('id', $query->meter->id)->update([
                                'status_id' => 0
                            ]);
                            break;
                        }
                        case 'set_real_time':{
                            break;
                        }

                        default:
                            echo 'Unknown request type in response';
                            die;
                    }

                    /** Set instruction as completed */
                    if(!empty($query->instruction)){
                        Instruction::where('id', $query->instruction->id)->update([
                            'query_id' => $query->id,
                            'completed_at' => Carbon::now(),
                            'status' => 1
                        ]);
                    }

                    /** Set query as completed */
                    Query::where('id', $query->id)->update([
                        'completed_at' => Carbon::now(),
                        'response' => json_encode($request->all())
                    ]);

                    /** Clear meter request pended */
                    Meter::where('id', $query->meter->id)->update([
                        'request_pended' => null
                    ]);

                    echo "<xml version=\"1.0\">\n
                    \t<type>{$this->param}</type>\n
                    \t<msgstatus>ok</msgstatus>\n
                    \t<queryid>{$this->query_id}</queryid>
                  </xml>";
                    die;
                }
            }
        }
    }
}
