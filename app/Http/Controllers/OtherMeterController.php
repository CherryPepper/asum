<?php

namespace App\Http\Controllers;

use App\Http\Requests\OValueAddRequest;
use App\Http\Requests\TotalValueRequest;
use App\Models\OControlValue;
use App\Models\OMeter;
use App\Models\OUserMeter;
use App\Models\OValue;

class OtherMeterController extends Controller
{
    public function getTotalValue(){
        $data['meters'] = OMeter::all();
        $data['values'] = OControlValue::with(['type'])->orderBy('id', 'desc')
            ->paginate(8);
        $data['current_date'] = $this->getOmetersCurrentDate();

        return view('other_meters.total_value', $data);
    }

    public function postTotalValue(TotalValueRequest $request){
        /** Update price for meter */
        OMeter::where('id', $request->type_id)
            ->update([
                'price' => $request->price
            ]);

        /** Get current date for other meters */
        $current_date = $this->getOmetersCurrentDate();

        /** Save or Update control value */
        OControlValue::where('created_at', $current_date)
            ->where('type_id', $request->type_id)->orderBy('id', 'desc')
            ->updateOrCreate([
                'type_id' => $request->type_id,
                'created_at' => $current_date
            ],[
                'type_id' => $request->type_id,
                'difference' => $request->difference,
                'accruals' => $request->price*$request->difference,
                'created_at' => $current_date
            ]);

        /** Calculate users values */
        OValue::calculateValues($request->type_id);

        return redirect()->back()->with('toast_messages', [
            [
                'status' => 'success',
                'message' => 'Показание успешно добавлено'
            ]
        ]);
    }

    public function getUserValue($user_id){
        $data['meters'] = OUserMeter::with(['type'])->where('user_id', $user_id)->get();
        $data['user_id'] = $user_id;
        $data['current_date'] = $this->getOmetersCurrentDate();

        return view('other_meters.add-value', $data);
    }

    public function postUserValue(OValueAddRequest $request){
        $result = OValue::addValue($request);

        if(!empty($result)) return $result;

        OValue::calculateValues($request->type_id);

        return [
            'status' => 'success',
            'message' => 'Показания успешно добавлены'
        ];
    }
}
