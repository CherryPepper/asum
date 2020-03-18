<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Meter;
use App\Models\MetersValue;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\User;
use DB;

class LossController extends Controller
{
    public function getReport($id = 0, Request $request){
        $data['parent_id'] = $id;
        $data['date_from'] = !empty($request->date_from) ? $request->date_from : Carbon::today()->subDay(1)->format('d.m.Y H:i');
        $data['date_to'] = !empty($request->date_to) ? $request->date_to : Carbon::today()->format('d.m.Y H:i');

        $data['meters'] = Meter::where('meters.parent_id', $id)->whereIn('type_id', [2,3,4])
            ->where('meters.display', 1)->joinAddress()->checkAccess()->orderBy('id', 'desc')->paginate(12);
        $data['parents'] = $this->getParentsForBreadcrumbs($id);

        $data['meters']->getCollection()->transform(function ($meter) use($data) {
            $model = new MetersValue();

            $date_from = Carbon::parse($data['date_from'])->format('Y-m-d H:i:s');
            $date_to = Carbon::parse($data['date_to'])->format('Y-m-d H:i:s');

            $child_meters = Meter::whereIn('id', explode(',', $meter->child_path))->where('type_id', 1)
                ->where('meters.display', 1)->orderBy('id', 'DESC')->get();

            $childs = new \stdClass();
            $childs->without_request = 0;
            $childs->with_error = 0;
            $childs->sum_difference = 0;

            foreach ($child_meters as $child){
                $model->setTable('meter_id_'.$child->id);

                $child = $model->select([
                    \DB::raw("(SELECT COUNT(id) FROM {$model->table} WHERE query_id IS NULL AND time_point >= '{$date_from}'
                    AND time_point <= '{$date_to}') AS without_request"),
                    \DB::raw("(SELECT COUNT(id) FROM {$model->table} WHERE error_code IS NOT NULL AND time_point >= '{$date_from}'
                    AND time_point <= '{$date_to}') AS with_error"),
                    \DB::raw("(SELECT SUM(difference) FROM {$model->table} WHERE time_point >= '{$date_from}' 
                    AND time_point <= '{$date_to}') AS sum_difference")
                ])->first();

                $childs->without_request += $child->without_request;
                $childs->with_error += $child->with_error;
                $childs->sum_difference += $child->sum_difference;
            }

            $model->setTable('meter_id_'.$meter->id);
            $control_meter = $model->select([
                \DB::raw("(SELECT COUNT(id) FROM {$model->table} WHERE query_id IS NULL AND time_point >= '{$date_from}'
                    AND time_point <= '{$date_to}') AS without_request"),
                \DB::raw("(SELECT COUNT(id) FROM {$model->table} WHERE error_code IS NOT NULL AND time_point >= '{$date_from}'
                    AND time_point <= '{$date_to}') AS with_error"),
                \DB::raw("(SELECT SUM(difference) FROM {$model->table} WHERE time_point >= '{$date_from}' 
                    AND time_point <= '{$date_to}') AS sum_difference")
            ])->first();

            $meter->childs = $childs;
            $meter->childs_cnt = sizeof($child_meters);
            $meter->without_request = $control_meter->without_request;
            $meter->with_error = $control_meter->with_error;
            $meter->sum_difference = $control_meter->sum_difference;

            return $meter;
        });

        $data['menu_uri'] = route('report.loss');

        return view('reports.loss', $data);
    }

    public function getChildMeters($id, Request $request){
        $data['parent_id'] = $id;
        $data['date_from'] = !empty($request->date_from) ? $request->date_from : Carbon::today()->subDay(1)->format('d.m.Y H:i');
        $data['date_to'] = !empty($request->date_to) ? $request->date_to : Carbon::today()->format('d.m.Y H:i');

        $data['current_parent'] = Meter::where('meters.id', $id)->whereIn('type_id', [2,3,4])
            ->where('meters.display', 1)->joinAddress()->checkAccess()->first();

        $data['parents'] = $this->getParentsForBreadcrumbs($id);

        $data['meters'] = Meter::joinAddress()->checkAccess()->where('type_id', 1)
            ->whereIn('meters.id', @explode(',', $data['current_parent']->child_path))
            ->where('meters.display', 1)->orderBy('id', 'desc')->paginate(12);

        $data['meters']->getCollection()->transform(function ($meter) use($data) {
            $model = new MetersValue();

            $date_from = Carbon::parse($data['date_from'])->format('Y-m-d H:i:s');
            $date_to = Carbon::parse($data['date_to'])->format('Y-m-d H:i:s');

            $model->setTable('meter_id_'.$meter->id);
            $current_meter = $model->select([
                \DB::raw("(SELECT COUNT(id) FROM {$model->table} WHERE query_id IS NULL AND time_point >= '{$date_from}'
                    AND time_point <= '{$date_to}') AS without_request"),
                \DB::raw("(SELECT COUNT(id) FROM {$model->table} WHERE error_code IS NOT NULL AND time_point >= '{$date_from}'
                    AND time_point <= '{$date_to}') AS with_error"),
                \DB::raw("(SELECT SUM(difference) FROM {$model->table} WHERE time_point >= '{$date_from}' 
                    AND time_point <= '{$date_to}') AS sum_difference")
            ])->first();

            $meter->without_request = $current_meter->without_request;
            $meter->with_error = $current_meter->with_error;
            $meter->sum_difference = $current_meter->sum_difference;

            return $meter;
        });

        $data['menu_uri'] = route('report.loss');

        return view('reports.loss-childs', $data);
    }

    public function getPoints($id, Request $request){
        $data['parent_id'] = $id;
        $data['date_from'] = !empty($request->date_from) ? $request->date_from : Carbon::today()->subDay(1)->format('d.m.Y H:i');
        $data['date_to'] = !empty($request->date_to) ? $request->date_to : Carbon::today()->format('d.m.Y H:i');
        $data['parents'] = $this->getParentsForBreadcrumbs($id);

        $data['current_meter'] = Meter::where('meters.id', $id)->joinAddress()->checkAccess()
            ->where('meters.display', 1)->first();

        if(!empty($data['current_meter'])){
            $model = new MetersValue();
            $model->setTable('meter_id_'.$data['current_meter']->id);

            $date_from = Carbon::parse($data['date_from'])->format('Y-m-d H:i:s');
            $date_to = Carbon::parse($data['date_to'])->format('Y-m-d H:i:s');

            $data['points'] = $model->where('time_point', '>=', $date_from)
                ->where('time_point', '<=', $date_to)
                ->orderBy('time_point', 'asc')
                ->paginate(12);

            $data['stats'] = $model->select([
                \DB::raw("(SELECT COUNT(id) FROM {$model->table} WHERE query_id IS NULL AND time_point >= '{$date_from}'
                    AND time_point <= '{$date_to}') AS without_request"),
                \DB::raw("(SELECT COUNT(id) FROM {$model->table} WHERE error_code IS NOT NULL AND time_point >= '{$date_from}'
                    AND time_point <= '{$date_to}') AS with_error"),
                \DB::raw("(SELECT SUM(difference) FROM {$model->table} WHERE time_point >= '{$date_from}' 
                    AND time_point <= '{$date_to}') AS sum_difference")
            ])->first();
        }

        $data['menu_uri'] = route('report.loss');

        return view('reports.loss-points', $data);
    }
}
