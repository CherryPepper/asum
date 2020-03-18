<?php

namespace App\Http\Controllers\Backend;

use App\Models\Backend\Instruction;
use App\Models\Backend\Procedure;
use App\Models\Backend\Query;
use App\Models\Backend\TempRow;
use App\Models\Meter;
use App\Models\Tozelesh\TzObject;
use Carbon\Carbon;
use DB;

class ScheduleController extends Controller
{
    public function __construct($request){
        parent::__construct($request);

        $procedures = Procedure::where('complete_time', '<=', $this->time)->where('enabled', 1)->get();

        foreach ($procedures as $procedure)
            $this->{$procedure->method}($procedure);
    }

    private function createRows($procedure){
        Procedure::where('id', $procedure->id)->update([
            'complete_time' => Carbon::tomorrow()->addMinutes(5)
        ]);

        $db_config = config('database.connections.db_meters_value');

        $meters = Meter::where('status_id', '!=', 3)->where('type_id', '!=', 4)->get();
        $time_end = strtotime(date('d.m.Y 00:00:00', strtotime($this->time)+86400));
        $i = 1;
        $inc = 1;
        $sql_tmp = '';
        $sql_val = '';

        foreach ($meters as $meter){
            $time_start = strtotime(date('d.m.Y 00:00:00', strtotime($this->time)))+900;

            $sql_val .= ' insert into meter_id_'.$meter->id.' (time_point, time_of_day, meter_id) values';

            while ($time_start <= $time_end){
                $point = date('Y-m-d H:i:s', $time_start);
                $time_of_day = date('H', $time_start)*60+date('i', $time_start);

                $sql_tmp .= "('{$point}', {$meter->id})";
                $sql_tmp .= ($i != 500) ? ',' : ';';

                $sql_val .= "('{$point}', {$time_of_day}, {$meter->id})";
                $sql_val .= ($time_start < $time_end) ? ',' : ';';

                if($i == 500){
                    $sql_tmp = 'insert into backend_temp_rows (point, meter_id) values'.$sql_tmp;
                   DB::insert($sql_tmp);

                    $sql_tmp = '';
                    $i = 0;
                }

                $time_start += 900;
                $i++;
            }

            $inc++;
        }

        if(!empty($sql_tmp))
            DB::insert('insert into backend_temp_rows (point, meter_id) values'.rtrim($sql_tmp, ','));
        if(!empty($sql_val)){
            $db = mysqli_connect($db_config['host'],$db_config['username'], $db_config['password'], $db_config['database']);

            mysqli_multi_query($db, $sql_val);
            mysqli_close($db);
        }

        /** Delete old rows and queries */
        TempRow::where('point', '<', Carbon::parse($this->time)->subDays(3))->delete();
        Query::where('created_at', '<', Carbon::parse($this->time)->subMonths(3))->delete();
    }

    private function setRealTime($procedure){
        Procedure::where('id', $procedure->id)->update([
            'complete_time' => Carbon::now()->addHours(3)
        ]);

        $instructions = [];
        $meters = Meter::where('status_id', '!=', 3)->where('type_id', '!=', 4)->get();

        foreach ($meters as $meter){
            $instructions[] = [
                'meter_id' => $meter->id,
                'action_id' => 2,
                'created_at' => Carbon::now(),
                'priority' => 3
            ];
        }

        Instruction::where([
            'action_id' => 2,
            'status' => 0
        ])->delete();

        $chunks = collect($instructions)->chunk(500);
        $chunks->each(function($chunk){
            Instruction::insert($chunk->toArray());
        });
    }

    private function checkPendedRequests($procedure){
        Procedure::where('id', $procedure->id)->update([
            'complete_time' => Carbon::now()->addMinutes(10)
        ]);

        Meter::where('request_pended', '<', Carbon::now()->subMinutes(10))->update([
            'request_pended' => null
        ]);
    }

    private function checkTozeleshOnOff($procedure){
        Procedure::where('id', $procedure->id)->update([
            'complete_time' => Carbon::now()->addMinutes(10)
        ]);

        $objects = TzObject::with('meter')->whereNotNull('time_on')
            ->whereNotNull('time_off')->where('display', 1)->get();
        $current_datetime = Carbon::now();
        $current_date = Carbon::now()->toDateString();
        $instructions = [];

        foreach ($objects as $object){
            $tmp_on = Carbon::parse($current_date.' '.$object->time_on.':00');
            $tmp_off = Carbon::parse($current_date.' '.$object->time_off.':00');

            if($current_datetime->gt($tmp_on) && ($object->meter->status_id == 0)){
                $instructions[] = [
                    'meter_id' => $object->meter->id,
                    'action_id' => 21, //on
                    'created_at' => Carbon::now(),
                    'priority' => 10
                ];
            }

            if($current_datetime->gt($tmp_off) && ($object->meter->status_id == 1)){
                $instructions[] = [
                    'meter_id' => $object->meter->id,
                    'action_id' => 20, //off
                    'created_at' => Carbon::now(),
                    'priority' => 10
                ];
            }
        }

        Instruction::insert($instructions);
    }
}
