<?php

namespace App\Http\Controllers;

use App\Helpers\Addresses;
use App\Http\Requests\AddControlMeterRequest;
use App\Http\Requests\MeterEditRequest;
use App\Http\Requests\MeterRegistrationRequest;
use App\Http\Requests\MoveMeterRequest;
use App\Models\MonthAssoc;
use App\Models\Rate;
use App\Models\Tozelesh\TzObject;
use Illuminate\Http\Request;
use App\Models\Backend\Instruction;
use App\Models\Backend\Query;
use App\Models\Backend\TempRow;
use App\Models\Meter;
use App\Models\MeterError;
use App\Models\MetersValue;
use App\Models\MobileOperator;
use Carbon\Carbon;
use DB;

class MeterController extends Controller
{
    public function getMetersForRegistration(){
        $data['meters'] = Meter::with(['address.parentRecursive', 'rate', 'user'])->whereNotNull('user_id')
            ->where('status_id', 3)->joinAddress()->checkAccess()->orderBy('id', 'DESC')
            ->paginate(10);

        return view('meters.list', $data);
    }

    public function getMeterRegistration($id){
        $data['meter'] = Meter::with(['address.parentRecursive', 'rate', 'user'])
            ->where('meters.id', $id)->where('status_id', 3)->joinAddress()->checkAccess()
            ->firstOrFail();
        $data['operators'] = MobileOperator::all();
        $data['menu_uri'] = route('meters.registration');

        return view('meters.registration', $data);
    }

    public function postMeterRegistration(MeterRegistrationRequest $request){
        Meter::where('id', $request->id)->update([
            'nst_lvl' => $request->nst_lvl,
            'ip_address' => $request->ip_address,
            'serial' => $request->serial,
            'operator_id' => $request->operator_id
        ]);

        return $this->createInstructions($request->id);
    }

    public function getAddControlMeter(){
        $data['operators'] = MobileOperator::all();
        $data['addresses'] = $this->getSelectAddresses();
        $data['rates'] = Rate::where('display', 1)->get();

        return view('meters.add-control', $data);
    }

    public function postAddControlMeter(AddControlMeterRequest $request){

        $meter = Meter::create([
            'nst_lvl' => 0,
            'type_id' => 2,
            'ip_address' => $request->ip_address,
            'serial' => $request->serial,
            'login' => \Config::get('app.meter_login'),
            'password' => \Config::get('app.meter_password'),
            'rate_id' => $request->rate_id,
            'operator_id' => $request->operator_id,
            'address_id' => Addresses::getLast($request->address),
            'description' => $request->description,
            'child_path' => $request->childs
        ]);

        return $this->createInstructions($meter->id);
    }

    public function getRegistrationProgress($id){
        $instruction = Instruction::where('meter_id', $id)->where('action_id', 4)->where('status', '!=', 0)
            ->first();

        if(!empty($instruction)){
            if($instruction->status == 1){
                if($instruction->meter->serial == json_decode($instruction->i_query->response)->sn){
                    $all = Instruction::where('parent_id', $instruction->meter->id)->count();
                    $completed = Instruction::where('parent_id', $instruction->meter->id)->where('status', 1)->count();
                    $failed = Instruction::where('parent_id', $instruction->meter->id)->where('status', 3)->count();

                    if($failed == 0){
                        if($all == $completed){
                            if($instruction->meter->tozelesh == 1){
                                $object = TzObject::where('meter_id', $instruction->meter->id)->first();
                                return [
                                    'type' => 'toast',
                                    'status' => 'success',
                                    'message' => 'Счетчик успешно зарегистрирован.',
                                    'tozelesh' => 1,
                                    'object_id' => $object->id
                                ];
                            }else{

                                /** Set parent path for child meters */
                                if($instruction->meter->type_id == 2){
                                    $childs = explode(',', $instruction->meter->child_path);

                                    if(!empty($childs))
                                        Meter::whereIn('id', $childs)->update([
                                            'parent_id' => $instruction->meter->id,
                                            'parent_path' => $instruction->meter->id
                                        ]);
                                }

                                return [
                                    'type' => 'toast',
                                    'status' => 'success',
                                    'message' => 'Счетчик успешно зарегистрирован. Переадресация через 5 сек'
                                ];
                            }
                        }

                        return [
                            'type' => 'progress',
                            'status' => 'success',
                            'all' => $all,
                            'completed' => $completed
                        ];
                    }else{
                        $this->rollback($id);

                        return [
                            'type' => 'toast',
                            'status' => 'error',
                            'message' => 'Во время регистрации счетчика, произошла неизвестная ошибка'
                        ];
                    }
                }else{
                    $input_serial = $instruction->meter->serial;
                    $response_serial = json_decode($instruction->i_query->response)->sn;
                    $this->rollback($id);

                    return [
                        'type' => 'toast',
                        'status' => 'error',
                        'message' => "Серийный номер на счетчике ({$response_serial}) отличается,
                                            от введенного вами ({$input_serial}). Попробуйте ещё раз."
                    ];
                }
            }elseif ($instruction->status == 3){
                $this->rollback($id);

                return [
                    'type' => 'toast',
                    'status' => 'error',
                    'message' => 'Во время проверки серийного номера, счетчик вернул ошибку - '.$instruction->code_error
                ];
            }else{
                $this->rollback($id);

                return [
                    'type' => 'toast',
                    'status' => 'error',
                    'message' => 'Во время проверки серийного номера, произошла неизвестная ошибка'
                ];
            }
        }
    }

    public function getStructure(Request $request, $id = 0, $move = null){
        $query = Meter::with(['address.parentRecursive', 'status', 'user', 'rate'])->joinAddress()->checkAccess()
            ->where('status_id', '!=', 4)
            ->where('meters.display', 1)
            ->where('status_id', '!=', 3)
            ->orderBy('type_id', 'desc')
            ->orderBy('serial', 'asc');

        if($move !== null){
            $query->where('type_id', 2);
            $query->where('meters.id', '!=', $move);
        }

        if(!empty($request->ip_address))
            $query->where('ip_address', $request->ip_address);
        if(!empty($request->serial))
            $query->where('serial', $request->serial);
        if(empty($request->ip_address) && empty($request->serial))
            $query->where('meters.parent_id', $id);

        /** Get parents for breadcrumbs */
        $data['parents'] = $this->getParentsForBreadcrumbs($id);

        $data['meters'] = $query->paginate(36);
        $data['move'] = $move;
        $data['menu_uri'] = route('meters.structure');

        return ($move == null) ? view('meters.structure', $data)
                : view('meters.move-meter', $data);
    }

    public function getMeterEdit($id){
        $data['meter'] = $query = Meter::with(['address.parentRecursive', 'status', 'user'])
            ->where('id', $id)->first();
        $data['operators'] = MobileOperator::all();

        return view('meters.meter-edit', $data);
    }

    public function postMeterEdit(MeterEditRequest $request){
        if(isset($request->meter_replacement)){
            /** Delete old points */
            $model = new MetersValue();
            $model->setTable('meter_id_'.$request->id);

            $model->whereNull('query_id')->delete();
            TempRow::where('meter_id', $request->id)->delete();

            /** Creating new points */
            $this->createPoints($request->id);
        }

        Meter::where('id', $request->id)->update([
            'ip_address' => $request->ip_address,
            'serial' => $request->serial,
            'login' => $request->login,
            'password' => $request->password,
            'operator_id' => $request->operator_id,
            'is_replacement' => isset($request->meter_replacement) ? 1 : 0
        ]);

        return [
            'status' => 'success',
            'message' => 'Счетчик был успешно отредактирован'
        ];
    }

    public function getRemoveFromDeferred($id){
        Meter::where('id', $id)->update([
            'deferred_time' => null,
            'long_deferred_cnt' => 0,
            'short_deferred_cnt' => 0,
            'not_response_cnt' => 0,
            'status_id' => 1
        ]);

        return [
            'status' => 'success',
            'message' => 'Счетчик был убран из отложенных'
        ];
    }

    public function getResetParent($id){
        $current_meter = Meter::where('id', $id)->first();

        /** Delete path in old parents */
        $this->deleteOldParents($current_meter);

        Meter::where('id', $id)->update([
            'parent_id' => 0,
            'parent_path' => null
        ]);

        return [
            'status' => 'success',
            'message' => 'Контрольный счетчик успешно сброшен'
        ];
    }

    public function postMoveMeter(MoveMeterRequest $request){
        $current_meter = Meter::where('id', $request->id)->first();
        $move_to = Meter::where('id', $request->moveTo)->first();

        /** Delete path in old parents */
        $this->deleteOldParents($current_meter);

        /** Add path in new parents */
        if(!empty($move_to->parent_path)){
            $new_parents = Meter::whereIn('id', explode(',', $move_to->parent_path))->get();

            foreach ($new_parents as $parent){
                $attributes = $parent->getAttributes();
                $attributes['child_path'] = $this->AddChildPath($current_meter->id, $attributes['child_path']);

                Meter::where('id', $attributes['id'])->update($attributes);
            }
        }

        /** Update child path in moveTo meter */
        $attributes = $move_to->getAttributes();
        $attributes['child_path'] = $this->AddChildPath($current_meter->id, $attributes['child_path']);

        Meter::where('id', $attributes['id'])->update($attributes);

        /** Update parents in current meter */
        $current_meter->parent_id = $move_to->id;
        $current_meter->parent_path = empty($move_to->parent_path) ? $move_to->id : $move_to->parent_path.','.$move_to->id;
        $current_meter->save();

        return [
            'status' => 'success',
            'message' => 'Счетчик успешно перенесен'
        ];
    }

    public function getMeterHistory($id, Request $request){
        $model = new MetersValue();
        $model->setTable('meter_id_'.$id);

        $query = false;
        $group = false;
        $data['next_frame'] = false;
        $data['current_frame'] = $request->frame;
        $data['separator'] = false;
        $data['id'] = $id;
        $data['date'] = $request->date;

        $months = MonthAssoc::all();
        $data['months'] = [];
        foreach ($months as $month)
            $data['months'][$month->num] = $month->title;

        switch ($request->frame){
            case 'year':{
                $data['next_frame'] = 'month';

                $group = DB::raw("YEAR(time_point)");
                $query = $model->select([
                    DB::raw("YEAR(time_point) point"),
                    DB::raw('SUM(difference) difference'),
                    DB::raw('MAX(value) value')
                ]);
                break;
            }
            case 'month':{
                $data['next_frame'] = 'day';
                $data['separator'] = '-';
                $date_start = $data['date'].'-01-01 00:00';
                $date_end = $data['date'].'-12-31 23:45';

                $group = DB::raw("MONTH(time_point)");
                $query = $model->select([
                        DB::raw("MONTH(time_point) point"),
                        DB::raw('SUM(difference) difference'),
                        DB::raw('MAX(value) value')
                    ])->where('time_point', '>=', $date_start)
                    ->where('time_point', '<=', $date_end);
                break;
            }
            case 'day':{
                $data['next_frame'] = 'hour';
                $data['separator'] = '-';

                $date_start = $data['date'].'-01 00:00';
                $date_end = $data['date'].'-'.Carbon::parse($date_start)->daysInMonth.' 23:45';

                $group = DB::raw("DAY(time_point)");
                $query = $model->select([
                    DB::raw("MAX(time_point) time_point"),
                    DB::raw("DAY(time_point) point"),
                    DB::raw('SUM(difference) difference'),
                    DB::raw('MAX(value) value')
                ])->where('time_point', '>=', $date_start)
                    ->where('time_point', '<=', $date_end);
                break;
            }
            case 'hour':{
                $data['next_frame'] = 'minute';
                $data['separator'] = ' ';

                $date_start = $data['date'].' 00:00';
                $date_end = $data['date'].' 23:45';

                $group = DB::raw("HOUR(time_point)");
                $query = $model->select([
                    DB::raw("MAX(time_point) time_point"),
                    DB::raw("HOUR(time_point) point"),
                    DB::raw('SUM(difference) difference'),
                    DB::raw('MAX(value) value')
                ])->where('time_point', '>=', $date_start)
                    ->where('time_point', '<=', $date_end);
                break;
            }
            case 'minute':{
                $date_start = $data['date'];
                $date_end = Carbon::parse($date_start)->addMinutes(45);

                $group = DB::raw("MINUTE(time_point)");
                $query = $model->select([
                    DB::raw("MAX(time_point) time_point"),
                    DB::raw("MINUTE(time_point) point"),
                    DB::raw('SUM(difference) difference'),
                    DB::raw('MAX(value) value')
                ])->where('time_point', '>=', $date_start)
                    ->where('time_point', '<=', $date_end);
                break;
            }
        }

        $data['values'] = $query->whereNotNull('difference')->groupBy($group)->paginate(12);
        $data['meter'] = Meter::with('address.parentRecursive')->where('id', $id)->first();

        return view('meters.history', $data);
    }

    private function deleteOldParents($meter){
        if(!empty($meter->parent_path)){
            $old_parents = Meter::whereIn('id', explode(',', $meter->parent_path))->get();

            foreach ($old_parents as $parent){
                $attributes = $parent->getAttributes();

                $child_path = explode(',', $attributes['child_path']);
                unset($child_path[array_search($meter->id, $child_path)]);

                $attributes['child_path'] = implode(',', $child_path);

                Meter::where('id', $attributes['id'])->update($attributes);
            }
        }
    }

    private function AddChildPath($meter_id, $child_path){
        $child_path = explode(',', $child_path);

        if(array_search($meter_id, $child_path) !== false)
            unset($child_path[array_search($meter_id, $child_path)]);
        if(isset($child_path[0]) && ($child_path[0] == false))
            unset($child_path[0]);

        $child_path[] = $meter_id;

        return implode(',', $child_path);
    }

    private function rollback($id){
        $meter = Meter::where('id', $id)->first();

        if($meter->type_id != 1)
            Meter::where('id', $id)->delete();
        else
            Meter::where('id', $id)->update([
                'parent_id' => 0,
                'status_id' => 3,
                'ip_address' => null,
                'serial' => null,
                'deferred_time' => null,
                'operator_id' => null,
                'request_pended' => null
            ]);

        Query::where('meter_id', $id)->delete();
        Instruction::where('meter_id', $id)->delete();
        MeterError::where('meter_id', $id)->delete();
        TempRow::where('meter_id', $id)->delete();

        $model = new MetersValue();
        $model->setTable('meter_id_'.$id);
        $model->dropTable();

        if($meter->tozelesh == 1){
            $object = TzObject::where('meter_id', $id)->first();
            TzObject::deleteObject($object->id);
        }


        return true;
    }
}
