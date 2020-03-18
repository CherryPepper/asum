<?php

namespace App\Http\Controllers;

use App\Http\Requests\OValueAddRequest;
use App\Models\MetersValue;
use App\Models\MonthAssoc;
use App\Models\Notification;
use App\Models\OUserMeter;
use App\Models\OValue;
use App\User;
use Carbon\Carbon;
use DB;

class UserController extends Controller
{
    public function getInfo(){
        $data['user'] = User::with(['meter.rate', 'meter.address.parentRecursive', 'meter.status'])
            ->where('users.id', $this->user->id)
            ->firstOrFail();
        $data['values'] = [];

        if($data['user']->meter->status_id !== 3){
            $model = new MetersValue();
            $model->setTable('meter_id_'.$data['user']->meter->id);

            $data['values'] = $model->select([
                DB::raw("MONTH(time_point) point"),
                DB::raw('SUM(difference) difference'),
                DB::raw('MAX(value) value')
            ])
                ->where('time_point', '<=', Carbon::now())
                ->where('time_point', '>=', Carbon::now()->subYear())
                ->groupBy(DB::raw("MONTH(time_point)"))
                ->orderBy('time_point', 'desc')
                ->paginate(12);

            $months = MonthAssoc::all();
            $data['months'] = [];
            foreach ($months as $month)
                $data['months'][$month->num] = $month->title;
        }


        return view('user.info', $data);
    }

    public function getOther_meters(){
        $data['current_date'] = $this->getOmetersCurrentDate();
        $data['meters'] = OUserMeter::with(['type'])->where('user_id', $this->user->id)->get();
        $data['values'] = OValue::with(['meter.type'])->where('user_id', $this->user->id)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')->paginate(8);


        return view('user.other-meters', $data);
    }

    public function postOther_meters(OValueAddRequest $request){
        $request->user_id = $this->user->id;

        $result = OValue::addValue($request);

        OValue::calculateValues($request->type_id);

        if(empty($result))
            $result = [
                'status' => 'success',
                'message' => 'Показания успешно добавлены'
            ];

        return redirect(route('user.other_meters'))->with('toast_messages', [$result]);
    }
}
