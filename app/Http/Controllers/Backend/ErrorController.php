<?php

namespace App\Http\Controllers\Backend;

use App\Models\Backend\Query;
use App\Models\Backend\TempRow;
use App\Models\MeterError;
use App\Models\MetersValue;
use App\Models\Backend\Instruction;
use App\Models\Meter;
use App\Models\Notification;
use Carbon\Carbon;

class ErrorController extends Controller
{
    public function __construct($request){
        parent::__construct($request);

        if(($this->query_id > 0) && ($this->param == 'error')){
            $query = Query::where('id', $this->query_id)->first();

            if(!empty($query)){
                $query_params = json_decode($query->request);

                if(isset($query_params->date) && isset($query_params->time))
                    $query_params->time_point = $query_params->date.' '.$query_params->time;

                switch ($this->value){
                    case 1001:{
                        $note = "Счетчик с сериным номером {$query->meter->serial} не отвечает";

                        if($this->request == 'deferred'){
                            $not_response_cnt = $query->meter->not_response_cnt+1;

                            Meter::where('id', $query->meter->id)->update([
                                'not_response_cnt' => $not_response_cnt,
                                'deferred_time' => Carbon::parse($this->time)->addHours(1)
                            ]);

                            if($not_response_cnt == 24){
                                Notification::insert([
                                    'chunk' => 0,
                                    'user_id' => 1,
                                    'title' => "Счетчик {$query->meter->serial} не отвечает",
                                    'message' => "Счетчик с серийным номером {$query->meter->serial} не отвечает более суток",
                                    'created_at' => Carbon::now(),
                                    'unread' => 1
                                ]);
                            }
                        }
                        else
                            Meter::where('id', $query->meter->id)->update([
                                'status_id' => 2,
                                'deferred_time' => Carbon::now(),
                                'not_response_cnt' => 1
                            ]);
                        break;
                    }
                    case 1003:{
                        $note = "Неверный формат запроса";
                        break;
                    }
                    case 2001:{
                        $note = "Серийный номер {$query->meter->serial} - неверный логин или пароль";
                        break;
                    }
                    case 3001:{
                        $note = "Серийный номер {$query->meter->serial} - нет данных для точки {$query_params->time_point}";

                        $model = new MetersValue();
                        $model->setTable('meter_id_'.$query->meter->id);

                        $model->where('time_point', $query_params->time_point)->update([
                            'query_id' => $query->id,
                            'error_code' => $this->value
                        ]);

                        break;
                    }
                    default:
                        $note = "Неизвестная ошибка";
                }

                /** Clear meter request pended */
                Meter::where('id', $query->meter->id)->update([
                    'request_pended' => null
                ]);

                /** Delete current point */
                if(isset($query_params->time_point)){
                    TempRow::where('meter_id', $query->meter->id)
                        ->where('point', $query_params->time_point)
                        ->delete();
                }

                /** Set instruction as completed with error */
                if($query->instruction_id > 0){
                    Instruction::where('id', $query->instruction_id)->update([
                        'status' => 3,
                        'completed_at' => Carbon::now(),
                        'code_error' => $this->value,
                        'query_id' => $query->id
                    ]);
                }

                /** Insert error for report */
                MeterError::insert([
                    'meter_id' => $query->meter->id,
                    'error_code' => $this->value,
                    'note' => $note,
                    'created_at' => Carbon::now()
                ]);

                /** Set query as completed with error */
                Query::where('id', $query->id)->update([
                    'completed_at' => Carbon::now(),
                    'code_error' => $this->value,
                    'response' => json_encode($request->all())
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
