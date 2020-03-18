<?php

namespace App\Http\Controllers;

use App\Models\Backend\Instruction;
use Carbon\Carbon;

class MeterInstructionController extends Controller
{
    public function getSetStatus($meter_id, $set){
        $act = ($set == 0) ? ['id' => 20, 'toast' => 'Выключения'] : ['id' => 21, 'toast' => 'Включения'];

        $instruction = [
            'meter_id' => $meter_id,
            'action_id' => $act['id'], //on/off
            'created_at' => Carbon::now(),
            'priority' => 10
        ];

        $id = Instruction::create($instruction)->id;

        return [
            'id' => $id,
            'status' => 'success',
            'message' => "Были созданы инструкции для {$act['toast']} счетчика"
        ];
    }

    public function getRefreshValue($meter_id){
        $instruction = [
            'meter_id' => $meter_id,
            'action_id' => 3, //short_data
            'created_at' => Carbon::now(),
            'priority' => 10,
            'parent_id' => $meter_id
        ];

        $id = Instruction::create($instruction)->id;

        return [
            'id' => $id,
            'status' => 'success',
            'message' => "Были созданы инструкции для обновления показания"
        ];
    }

    public function getCheckInstruction($id){
        $instruction = Instruction::with('meter')->where('id', $id)->first();
        $data_json = [];

        if ($instruction->status == 3){
            $data_json['status'] = 'error';
            $data_json['message'] = 'Во время опроса счетчик вернул ошибку - '.$instruction->code_error;
        }

        switch ($instruction->action_id){
            // Set meter status
            case 20:
            case 21:{
                if($instruction->status == 1){
                    $data_json['status'] = 'success';
                    $data_json['message'] = 'Статус счетчика успешно изменен';
                }

                $data_json['setStatus'] = [
                    'meter_id' => $instruction->meter_id,
                    'status' => ($instruction->action_id == 20) ? 0 : 1
                ];

                break;
            }
            //Refresh value
            case 3:{
                if($instruction->status == 1){
                    $data_json['status'] = 'success';
                    $data_json['message'] = 'Показание счетчика успешно обновлено';
                }

                $data_json['newVal'] = [
                    'meter_id' => $instruction->meter_id,
                    'value' => $instruction->meter->value
                ];
                break;
            }
        }

        return $data_json;
    }
}