<?php

namespace App\Http\Controllers;

use App\Helpers\Addresses;
use App\Models\Address;
use App\Models\Backend\TempRow;
use App\Models\Meter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Backend\Action;
use App\Models\Backend\Query;

class MonitoringController extends Controller
{
    public function getRequests(Request $request){
        $data['actions'] = Action::where('status', 1)->orderBy('id', 'ASC')->get();
        $data['action_id'] = $request->action_id;
        $data['status'] = $request->status;
        $data['addresses'] = $this->getSelectAddresses();

        if(((int)$request->action_id > 0)){
            $query = Query::where('action_id', $request->action_id);

            if((int)$request->status > 0){
                switch ($request->status){
                    case 1:{
                        $query->where('code_error', 0)->whereNotNull('completed_at');
                        break;
                    }
                    case 2:{
                        $query->whereNull('completed_at');
                        break;
                    }
                    case 3:{
                        $query->where('code_error', '>', 0);
                        break;
                    }
                }
            }

            if(isset($request->address['region']) && !empty($request->address['region'])){
                $addresses = Address::where('id', Addresses::getLast($request->address))->first()->path_childs;

                $query->join('meters as m', 'm.id', '=', 'meter_id')->whereIn('address_id', explode(',', $addresses));
            }

            if(!empty($request->date_from))
                $query->where('backend_queries.created_at', '>=', Carbon::parse($request->date_from)->format('Y-m-d'));

            if(!empty($request->date_to))
                $query->where('backend_queries.created_at', '<=', Carbon::parse($request->date_to)->format('Y-m-d'));

            $data['queries'] = $query->orderBy('backend_queries.id', 'DESC')->paginate(20);
        }

        return view('monitoring.requests', $data);
    }

    public function getMeters(Request $request){
        $data = [];

        $query = Meter::with(['address.parentRecursive'])->where('meters.display', 1)->joinAddress();

        switch ($request->type){
            case 1:{
                $query->where('status_id', 2)->orderBy('deferred_time', 'DESC');

                break;
            }
            case 2:{
                $points = TempRow::select([\DB::raw('MAX(point)'), 'meter_id'])->where('point', '<', $this->current_point)
                    ->groupBy('meter_id')->get();

                $id_list = [];
                foreach ($points as $point)
                    $id_list[] = $point->meter_id;

                $query->whereIn('status_id', [1,2])->whereIn('meters.id', $id_list)->orderBy('a.hash', 'asc');

                break;
            }

            default:{
                $query->where('status_id', 1)->orderBy('a.hash', 'asc');
            }
        }

        if(isset($request->address['region']) && !empty($request->address['region'])){
            $addresses = Address::where('id', Addresses::getLast($request->address))->first()->path_childs;
            $query->whereIn('address_id', explode(',', $addresses));
        }

        $data['types'] = [
            0 => [
                'view' => '_online',
                'title' => 'Онлайн'
            ],
            1 => [
                'view' => '_deferred',
                'title' => 'Отложенные'
            ],
            2 => [
                'view' => '_missed',
                'title' => 'Пропущенные точки'
            ]
        ];

        $data['addresses'] = $this->getSelectAddresses();
        $data['meters'] = $query->paginate(20);

        return view('monitoring.meters', $data);
    }

    public function getMissedPoints($id){
        $data['points'] = TempRow::with(['meter'])->where('meter_id', $id)
            ->orderBy('id', 'ASC')->paginate(8);

        return view('monitoring.meter-missed-points', $data);
    }
}
