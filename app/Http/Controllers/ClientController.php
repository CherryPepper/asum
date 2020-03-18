<?php

namespace App\Http\Controllers;

use App\Helpers\Addresses;
use App\Http\Requests\CreateClientRequest;
use App\Http\Requests\EditClientRequest;
use App\Http\Requests\PasswordConfirmationRequest;
use App\Models\Address;
use App\Models\OMeter;
use App\Models\OUserMeter;
use App\Models\OValue;
use Illuminate\Http\Request;
use App\Models\Rate;
use App\Models\Meter;
use App\User;
use Carbon\Carbon;

class ClientController extends Controller
{
    public function getView($id){
        $data['user'] = User::with(['meter.rate', 'meter.address.parentRecursive', 'meter.status'])
            ->where('users.id', $id)->deletedForAdmin()->joinAddress()->checkAccess()
            ->firstOrFail();
        $data['other_meters'] = OUserMeter::with('type')->where('user_id', $id)
            ->where('display', 1)->get();

        $data['other_meters']->transform(function($meter){
            $value = OValue::where('meter_id', $meter->id)->orderBy('id', 'desc')
                ->first();

            $meter->value = !empty($value) ? $value->value : 0;

            return $meter;
        });

        $data['menu_uri'] = route('client.list');

        return view('client.view', $data);
    }

    public function getList(Request $request){
        $query = User::with(['meter.rate', 'meter.address.parentRecursive'])
            ->where('role_id', 4)->joinAddress()->deletedForAdmin()->checkAccess()
            ->orderBy('a.hash', 'asc');

        if(Addresses::getLast($request->address)){
            $childs = Address::where('id', Addresses::getLast($request->address))->first()->path_childs;
            $query->whereIn('a.id', explode(',', $childs));
        }
        if($request->contract)
            $query->where('contract', 'LIKE', "%{$request->contract}%");
        if($request->name)
            $query->where(function ($query) use ($request){
                $query->where('first_name', 'LIKE', "%{$request->name}%")
                    ->orWhere('last_name', 'LIKE', "%{$request->name}%");
            });


        $data['addresses'] = $this->getSelectAddresses();
        $data['users'] = $query->paginate(10);

        return view('client.list', $data);
    }

    public function getCreate(){
        $data['rates'] = Rate::where('display', 1)->get();
        $data['addresses'] = $this->getSelectAddresses();
        $data['other_meters'] = OMeter::all();

        return view('client.create', $data);
    }

    public function postCreate(CreateClientRequest $request){
        //Create User
        $user = User::create(collect($request->all())->only([
            'first_name', 'last_name', 'email', 'passport',
            'passport_mvd', 'contract', 'login'
        ])->merge([
            'password' => \Hash::make($request->password), 'role_id' => 4]
        )->toArray());

        //Create Meter
        Meter::create([
            'user_id' => $user->id,
            'rate_id' => $request->rate_id,
            'address_id' => Addresses::getLast($request->address),
            'login' => \Config::get('app.meter_login'),
            'password' => \Config::get('app.meter_password')
        ]);

        //Other meters
        $this->createOthermeters($request, ['user_id' => $user->id]);

        return redirect()->back()->with('toast_messages', [
            [
                'status' => 'success',
                'message' => 'Абонент успешно добавлен'
            ]
        ]);
    }

    public function getEdit($id){
        $data['rates'] = Rate::where('display', 1)->get();
        $data['user'] = User::with(['meter.rate', 'meter.address.parentRecursive'])
            ->where('users.id', $id)->deletedForAdmin()->joinAddress()->checkAccess()
            ->firstOrFail();
        $data['address'] = Addresses::AdrObj($data['user']->meter->address);
        $data['addresses'] = $this->getSelectAddresses($data['address']);
        $data['other_meters'] = OMeter::all();
        $data['o_user_meters'] = [];

        $o_user_meters = OUserMeter::where('user_id', $id)
            ->where('display', 1)->get();

        foreach ($o_user_meters as $meter){
            $value =  OValue::where('meter_id', $meter->id)->orderBy('id', 'desc')
                ->first();

            $meter->value = !empty($value) ? $value->value : 0;

            $data['o_user_meters'][$meter->type_id] = $meter;
        }

        $data['menu_uri'] = route('client.list');

        return view('client.edit', $data);
    }

    public function postEdit(EditClientRequest $request){
        //Edit User
        $data = collect($request->all())->only([
            'first_name', 'last_name', 'email', 'passport',
            'passport_mvd', 'contract', 'login'
        ]);
        if(isset($request->password))
            $data = $data->merge(['password' => \Hash::make($request->password)]);

        User::where('id', $request->id)->update($data->toArray());

        //Edit Meter
        Meter::where('user_id', $request->id)->update([
            'rate_id' => $request->rate_id,
            'address_id' => Addresses::getLast($request->address)
        ]);

        // Other meters
        $this->createOthermeters($request, ['user_id' => $request->id, 'is_edit' => true]);

        return redirect(route('client.view', ['id' => $request->id]))->with('toast_messages', [
            [
                'status' => 'success',
                'message' => 'Абонент успешно отредактирован'
            ]
        ]);
    }

    public function postDelete(PasswordConfirmationRequest $request){
        User::where('id', $request->id)->update([
            'display' => 0,
            'deleted_at' => Carbon::now()
        ]);

        Meter::where('user_id', $request->id)->update([
            'display' => 0,
            'status_id' => 3,
            'ip_address' => null,
            'serial' => null,
            'deleted_at' => Carbon::now()
        ]);

        OUserMeter::where('user_id', $request->id)->update([
            'display' => 0
        ]);

        OValue::where('user_id', $request->id)->update([
            'display' => 0
        ]);

        return response()->json([
            'status' => 'success',
            'url' => route('client.list')
        ]);
    }

    public function postDeletePermanently(PasswordConfirmationRequest $request){
        User::where('id', $request->id)->delete();
        Meter::where('user_id', $request->id)->delete();
        OUserMeter::where('user_id', $request->id)->delete();
        OValue::where('user_id', $request->id)->delete();

        return response()->json([
            'status' => 'success',
            'url' => route('client.list')
        ]);
    }

    public function getRecover($id){
        User::where('id', $id)->update([
            'display' => 1,
            'deleted_at' => null
        ]);

        Meter::where('user_id', $id)->update([
            'display' => 1,
            'deleted_at' => null
        ]);

        return redirect()->back()->with('toast_messages', [
            [
                'status' => 'success',
                'message' => 'Абонент успешно восстановлен'
            ]
        ]);
    }

    private function createOthermeters($request, $data){
        $selected_arr = [];

        if(!empty($request->other_meters)) {
            foreach ($request->other_meters as $type_id => $meter)
                $selected_arr[] = $type_id;
        }

        if(isset($data['is_edit'])){
            OUserMeter::where('user_id', $data['user_id'])->whereNotIn('type_id', $selected_arr)->delete();
            OValue::where('user_id', $data['user_id'])->whereNotIn('type_id', $selected_arr)->delete();
        }

        if(!empty($request->other_meters)){
            $o_values = [];

            foreach ($request->other_meters as $type_id=>$meter){
                $check_meter = OUserMeter::where([
                    'user_id' => $data['user_id'],
                    'type_id' => $type_id
                ])->first();

                $new_meter = OUserMeter::updateOrCreate([
                    'user_id' => $data['user_id'],
                    'type_id' => $type_id
                ], [
                    'user_id' => $data['user_id'],
                    'type_id' => $type_id,
                    'serial' => $meter['serial'],
                    'created_at' => Carbon::now()
                ]);

                if(empty($check_meter)){
                    $o_values[] = [
                        'meter_id' => $new_meter->id,
                        'user_id' => $data['user_id'],
                        'type_id' => $type_id,
                        'value' => $meter['value'],
                        'difference' => 0,
                        'accruals' => 0,
                        'manually' => 0,
                        'created_at' => Carbon::parse($this->getOmetersCurrentDate())->subMonth()
                    ];
                }
            }

            OValue::insert($o_values);
        }

        return true;
    }
}
