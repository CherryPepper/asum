<?php

namespace App\Http\Controllers\Backend;

use App\Models\Backend\Query;
use App\Models\Backend\TempRow;
use App\Models\Backend\Vars;
use App\Models\MobileOperator;
use App\Models\Backend\Instruction;
use App\Models\Meter;
use Carbon\Carbon;

class QueryController extends Controller
{
    public function __construct($request){
        parent::__construct($request);

        /** Get Query */
        if($this->param == 'get_query'){
            $this->operator_id = MobileOperator::where('name_en', $this->operator)->first()->id;
            $query_params = [];

            /**  Get instruction */
            $instruction = Instruction::select('bi.*')
                ->from('backend_instructions as bi')
                ->where('bi.status', 0)
                ->where('m.request_pended', null)
                ->where('m.operator_id', $this->operator_id)
                ->join('meters as m', 'bi.meter_id', '=', 'm.id')
                ->orderBy('bi.priority', 'DESC')
                ->orderBy('bi.id', 'ASC')
                ->first();

            /** Allow instructions only for deferred sender */
            if($this->request != 'deferred')
                $instruction = null;

            if(!empty($instruction)){
                $query_params['param'] = $instruction->action->name;
                $query_params['target'] = $instruction->meter->ip_address;

                switch ($instruction->action->name){
                    case 'get_multi_info':{
                        $instruction->request = json_encode([
                            'meter_ip' => $instruction->meter->ip_address,
                            'serial' => $instruction->meter->serial,
                            'time' => $this->time->format('H:i'),
                            'date' => $this->time->format('Y/m/d')
                        ]);

                        $query_params['param'] = 'short_data';
                        $query_params['action'] = 'get_multi_info';
                        $query_params['serial'] = $instruction->meter->serial;
                        $query_params['time'] = $this->time->format('H:i');
                        $query_params['date'] = $this->time->format('Y/m/d');
                        break;
                    }
                    case 'set_real_time':{
                        $instruction->request = json_encode([
                            'meter_ip' => $instruction->meter->ip_address,
                            'time' => $this->time->format('Y/m/d,H:i:s'),
                            'login' => $instruction->meter->login,
                            'password' => $instruction->meter->password
                        ]);

                        $query_params['time'] = $this->time->format('Y/m/d,H:i:s');
                        $query_params['login'] = $instruction->meter->login;
                        $query_params['password'] = $instruction->meter->password;
                        $query_params['action'] = 'configuration';
                        break;
                    }
                    case 'short_data':{
                        $instruction->request = json_encode([
                            'meter_ip' => $instruction->meter->ip_address,
                            'serial' => $instruction->meter->serial
                        ]);

                        $query_params['serial'] = $instruction->meter->serial;
                        $query_params['action'] = 'get_info';
                        break;
                    }
                    case 'get_serial_meter':{
                        $instruction->request = json_encode([
                            'meter_ip' => $instruction->meter->ip_address,
                            'login' => $instruction->meter->login,
                            'password' => $instruction->meter->password
                        ]);

                        $query_params['login'] = $instruction->meter->login;
                        $query_params['password'] = $instruction->meter->password;
                        $query_params['action'] = 'get_info';
                        break;
                    }
                    case 'meter_on':{
                        $instruction->request = json_encode([
                            'meter_ip' => $instruction->meter->ip_address,
                            'serial' => $instruction->meter->serial
                        ]);

                        $query_params['serial'] = $instruction->meter->serial;
                        $query_params['action'] = 'do';
                        break;
                    }
                    case 'meter_off':{
                        $instruction->request = json_encode([
                            'meter_ip' => $instruction->meter->ip_address,
                            'serial' => $instruction->meter->serial
                        ]);

                        $query_params['serial'] = $instruction->meter->serial;
                        $query_params['action'] = 'do';
                        break;
                    }

                    default:{
                        echo 'Unknown request type';
                        die;
                    }
                }
            }else{/** Get meter for point request */
                /** Get current point */
                $query = TempRow::select('btr.*')
                    ->from('backend_temp_rows as btr')
                    ->join('meters as m', 'btr.meter_id', '=', 'm.id')
                    ->where('m.request_pended', null)
                    ->where('m.operator_id', $this->operator_id);

                switch ($this->request){
                    case 'missed':{
                        $query->where('m.status_id', 1)
                            ->where('btr.point', '<', $this->current_point);
                        break;
                    }
                    case 'online':{
                        $query->where('m.status_id', 1)
                            ->where('btr.point', $this->current_point);
                        break;
                    }
                    case 'deferred':{
                        $query->where('m.status_id', 2)
                            ->where('m.deferred_time', '<', $this->time)
                            ->where('btr.point', $this->current_point);
                        break;
                    }

                    default:
                        echo 'Unknown request type';
                        die;
                }
                /** Get current point END */

                $point = $query->first();
                $delay = Carbon::parse($this->current_point)->addMinutes(2);

                if(!empty($point) && ($this->time >= $delay)){
                    /** Prepare query params */
                    $query_params['time'] = Carbon::parse($point->point)->format('H:i');
                    $query_params['date'] = Carbon::parse($point->point)->format('Y/m/d');
                    $query_params['serial'] = $point->meter->serial;
                    $query_params['target'] = $point->meter->ip_address;
                    $query_params['action'] = 'get_multi_info';
                    $query_params['param'] = 'short_data';

                    /** Create instruction entity */
                    $instruction = new \stdClass();
                    $instruction->id = 0;
                    $instruction->request = json_encode([
                        'meter_ip' => $point->meter->ip_address,
                        'meter_serial' => $point->meter->serial,
                        'time_point' => $point->point
                    ]);
                    $instruction->action_id = 1;
                    $instruction->meter_id = $point->meter->id;
                }else{
                    echo "<xml version=\"1.0\"><command_wait>5</command_wait></xml>";
                    die;
                }
            }

            $query_params['query_id'] = Query::create([
                'meter_id' => $instruction->meter_id,
                'action_id' => $instruction->action_id,
                'request' => $instruction->request,
                'instruction_id' => $instruction->id,
                'created_at' => Carbon::now()
            ])->id;

            /** Mark request pended as true */
            Meter::where('id', $instruction->meter_id)->update([
                'request_pended' => Carbon::now()
            ]);

            $query_vars = Vars::all();

            /** Generate xml for meter query */
            $xml = "<xml version=\"1.0\">\n";
            foreach ($query_vars as $var)
                $xml .= isset($query_params[$var->key]) ? "<{$var->value}>{$query_params[$var->key]}</{$var->value}>" : '';
            $xml .= "\n</xml>";

            echo $xml;
            die;
        }
    }
}
