<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRateRequest;
use App\Http\Requests\PasswordConfirmationRequest;
use App\Models\Rate;
use App\Models\RateInterval;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RateController extends Controller
{
    public function getCreate(){
        $data = [];

        return view('rates.create', $data);
    }

    public function postCreate(CreateRateRequest $request){
        $rate = Rate::create(collect($request->all())->only([
            'title', 'type', 'description'
        ])->toArray());

        $data = [];
        foreach ($request->time_start as $key=>$value){
            $data[] = [
                'rate_id' => $rate->id,
                'time_start' => (int)$value*60,
                'time_end' => (int)$request->time_end[$key]*60,
                'price' => $request->price[$key]
            ];
        }

        RateInterval::insert($data);

        return redirect(route('rate.list'))->with('toast_messages', [
            [
                'status' => 'success',
                'message' => 'Тариф успешно добавлен'
            ]
        ]);
    }

    public function getList(Request $request){
        $query = Rate::with(['intervals'])->where('display', 1);

        if((int)$request->type)
            $query->where('type', (int)$request->type);

        $data['rates'] = $query->paginate(10);

        return view('rates.list', $data);
    }

    public function postDelete(PasswordConfirmationRequest $request){
        Rate::where('id', $request->id)->update([
            'display' => 0,
            'deleted_at' => Carbon::now()
        ]);

        return response()->json([
            'status' => 'success',
            'url' => route('rate.list')
        ]);
    }
}
