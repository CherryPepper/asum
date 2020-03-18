<?php

namespace App\Http\Controllers\Tozelesh;

use App\Helpers\Addresses;
use App\Http\Controllers\Controller;
use App\Http\Requests\TozeleshMeterRequest;
use App\Models\Meter;
use App\Models\Rate;
use App\Models\MobileOperator;
use App\Models\Tozelesh\TzObject;
use Carbon\Carbon;

class MeterRegistrationController extends Controller
{
    public function getMeterRegistration(){
        $data['operators'] = MobileOperator::all();
        $data['addresses'] = $this->getSelectAddresses();
        $data['rates'] = Rate::where('display', 1)->get();

        return view('tozelesh.meter-registration', $data);
    }

    public function postMeterRegistration(TozeleshMeterRequest $request){
        $meter = Meter::create([
            'nst_lvl' => 0,
            'type_id' => 1,
            'ip_address' => $request->ip_address,
            'serial' => $request->serial,
            'login' => \Config::get('app.meter_login'),
            'password' => \Config::get('app.meter_password'),
            'rate_id' => $request->rate_id,
            'operator_id' => $request->operator_id,
            'address_id' => Addresses::getLast($request->address),
            'description' => $request->description,
            'tozelesh' => 1
        ]);

        TzObject::create([
            'meter_id' => $meter->id,
            'coordinates' => $request->latlng,
            'created_at' => Carbon::now()
        ]);

        return $this->createInstructions($meter->id);
    }
}
