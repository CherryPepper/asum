<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Meter;
use App\Models\MetersValue;
use App\Models\Backend\TempRow;
use App\Models\Backend\Instruction;
use App\Models\Notification;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Repositories\NavigationRepository;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $user;
    public $current_point;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = User::userRole();
            $this->current_point = Carbon::parse(date('Y-m-d H:i:s', floor((time()-strtotime(Carbon::today()))
                    /900)*900+strtotime(Carbon::today())));

            $new_notifications = Notification::where('user_id', $this->user->id)->where('unread', 1)
                ->orderBy('created_at', 'desc')->limit(3)->get();

            $new_notifications_cnt = Notification::where('user_id', $this->user->id)->where('unread', 1)
                ->count('id');

            \View::share([
                'new_notifications' => $new_notifications,
                'new_notifications_cnt' => $new_notifications_cnt,
                'userInfo' => $this->user,
                'navigation' => NavigationRepository::getNavigation($this->user),
                'menu_uri' => url()->current()
            ]);

            return $next($request);
        });
    }

    public function getSelectAddresses($address = null){
        $data = [];

        $data['regions'] = Address::where([
            'parent_id' => 0,
            'display' => 1
        ])->get();

        if(old('address.region')){
            $old = [
                'region' => (int)old('address.region', @$address->region_id),
                'street' => (int)old('address.street', @$address->street_id),
                'home' => (int)old('address.home', @$address->home_id)
            ];
        }else{
            $old = [
                'region' => (int)app('request')->input('address.region', @$address->region_id),
                'street' => (int)app('request')->input('address.street', @$address->street_id),
                'home' => (int)app('request')->input('address.home', @$address->home_id)
            ];
        }

        if($old['region'])
            $data['streets'] = Address::where([
                'parent_id' => $old['region'],
                'display' => 1
            ])->get();

        if($old['street'])
            $data['homes'] = Address::where([
                'parent_id' => $old['street'],
                'display' => 1
            ])->get();

        if($old['home'])
            $data['apartments'] = Address::where([
                'parent_id' => $old['home'],
                'display' => 1
            ])->get();

        return $data;
    }

    public function getParentsForBreadcrumbs($id){
        $parents_arr = [];

        if($id > 0){
            $current_parent = Meter::where('id', $id)->first();

            if(!empty($current_parent)){
                $parents_array = explode(',', $current_parent->parent_path);
                $placeholders = implode(',',array_fill(0, count($parents_array), '?'));
                $parents_arr = Meter::whereIn('id', $parents_array)
                    ->orderByRaw("field(id,{$placeholders})", $parents_array)->get();
                $parents_arr[] = $current_parent;
            }
        }

        return $parents_arr;
    }

    public function getOmetersCurrentDate(){
        $current_date = Carbon::now();

        if($current_date->day < 20)
            $current_date = Carbon::parse($current_date->subMonth())->format('Y-m-20 00:00:00');
        else
            $current_date = Carbon::parse($current_date)->format('Y-m-20 00:00:00');

        return $current_date;
    }

    public function createInstructions($id){
        /** Create points for meter */
        $this->createPoints($id);

        /** Create instructions */
        $instructions = [
            [
                'meter_id' => $id,
                'action_id' => 4, //get_serial_meter
                'created_at' => Carbon::now(),
                'priority' => 10,
                'parent_id' => $id
            ],
            [
                'meter_id' => $id,
                'action_id' => 2, //set_real_time
                'created_at' => Carbon::now(),
                'priority' => 10,
                'parent_id' => $id
            ],
            [
                'meter_id' => $id,
                'action_id' => 21, //meter_on
                'created_at' => Carbon::now(),
                'priority' => 10,
                'parent_id' => $id
            ],
            [
                'meter_id' => $id,
                'action_id' => 3, //short_data
                'created_at' => Carbon::now(),
                'priority' => 10,
                'parent_id' => $id
            ],
        ];

        Instruction::insert($instructions);

        return [
            'status' => 'success',
            'message' => 'Были созданы инструкции для регистрации счетчика',
            'id' => $id
        ];
    }

    public function createPoints($meter_id){
        $model = new MetersValue();
        $model->setTable('meter_id_'.$meter_id);
        $model->createTable();

        $current_point = Carbon::parse(date('Y-m-d H:i:s',
            floor((time()-strtotime(Carbon::today()))/900)*900+strtotime(Carbon::today())));
        $temp_rows = [];
        $meter_points = [];

        for($i = $current_point; $i <= Carbon::tomorrow(); $i->addMinutes(15)){
            $temp_rows[] = [
                'point' => $i->toDateTimeString(),
                'meter_id' => $meter_id
            ];

            $meter_points[] = [
                'time_point' => $i->toDateTimeString(),
                'time_of_day' => ($i->hour*60)+$i->minute,
                'meter_id' => $meter_id
            ];
        }

        TempRow::insert($temp_rows);
        $model->insert($meter_points);

        return true;
    }
}
