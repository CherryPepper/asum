<?php

namespace App\Http\Controllers\Reports;

use App\Helpers\Addresses;
use App\Helpers\DateTime;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Meter;
use App\Models\MetersValue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use Excel;

class ConsumptionController extends Controller
{
    public function getReport(Request $request){
        $data['date_from'] = !empty($request->date_from) ? $request->date_from : Carbon::today()->subMonth(1)->format('d.m.Y');
        $data['date_to'] = !empty($request->date_to) ? $request->date_to : Carbon::today()->format('d.m.Y');
        $data['time_from'] = !empty($request->time_from) ? $data['date_from'].' '.$request->time_from.':00' : $data['date_from'].' 00:00:00';
        $data['time_to'] = !empty($request->time_to) ? $data['date_from'].' '.$request->time_to.':00' : $data['date_from'].' 23:45:00';
        $data['addresses'] = $this->getSelectAddresses();
        $data['current_frame'] = isset($request->frame) ? (int)$request->frame : 1;
        $data['all_months'] = DateTime::getAllMonths();

        $data['frames'] = [
            1 => '1 Месяц',
            2 => '1 Сутки',
            3 => '1 Час',
            4 => '15 Минут'
        ];

        /** Select meters for report */
        $meters = Meter::with('address.parentRecursive', 'rate')->joinAddress()->checkAccess()
            ->where('status_id', '!=', 3)->where('meters.display', 1)->orderBy('a.hash', 'asc');

        if(isset($request->address['region']) && !empty($request->address['region'])){

            $address_list = Address::where('id', Addresses::getLast($request->address))->first()->path_childs;
            $meters->whereIn('address_id', explode(',', $address_list));
        }

        $data['meters'] = ($request->excel == 1) ? $meters->get() : $meters->paginate(12);

        $collection = ($request->excel == 1) ? $data['meters'] : $data['meters']->getCollection();

        $collection->transform(function ($meter) use($data) {
            $model = new MetersValue();
            $model->setTable('meter_id_'.$meter->id);

            switch ($data['current_frame']){
                case 1:{
                    $values = $model->select([
                        DB::raw("MAX(time_point) time_point"),
                        DB::raw('SUM(difference) difference')
                    ])->where('time_point', '>=', Carbon::parse($data['date_from'])->format('Y-m-d 00:00:00'))
                        ->where('time_point', '<=', Carbon::parse($data['date_to'])->format('Y-m-d 23:45:00'))
                        ->groupBy(DB::raw("MONTH(time_point)"))->get();

                    break;
                }
                case 2:{
                    $values = $model->select([
                        DB::raw("MAX(time_point) time_point"),
                        DB::raw('SUM(difference) difference')
                    ])->where('time_point', '>=', Carbon::parse($data['date_from'])->format('Y-m-d 00:00:00'))
                        ->where('time_point', '<=', Carbon::parse($data['date_to'])->format('Y-m-d 23:45:00'))
                        ->groupBy(DB::raw("DAY(time_point)"))->orderBy('time_point', 'asc')->get();

                    break;
                }
                case 3:{
                    $values = $model->select([
                        DB::raw("MAX(time_point) time_point"),
                        DB::raw('SUM(difference) difference')
                    ])->where('time_point', '>=', Carbon::parse($data['time_from'])->format('Y-m-d H:00:00'))
                        ->where('time_point', '<=', Carbon::parse($data['time_to'])->format('Y-m-d H:45:00'))
                        ->groupBy(DB::raw("HOUR(time_point)"))->orderBy('time_point', 'asc')->get();

                    break;
                }
                case 4:{
                    $values = $model->select([
                        DB::raw("MAX(time_point) time_point"),
                        DB::raw('SUM(difference) difference')
                    ])->where('time_point', '>=', Carbon::parse($data['time_from'])->format('Y-m-d H:00:00'))
                        ->where('time_point', '<=', Carbon::parse($data['time_to'])->format('Y-m-d H:45:00'))
                        ->groupBy(DB::raw("time_point"))->orderBy('time_point', 'asc')->get();

                    break;
                }
            }

            $meter->values = $values;

            return $meter;
        });

        if($request->excel == 1){
            Excel::load(public_path('excel/consumption/template.xlsx'), function ($template) use($data){
                $template->sheet('report', function ($sheet) use ($data){

                        /** Report Body */
                        $row = 2;
                        foreach ($data['meters'] as $meter){

                            $total_difference = 0;
                            $values = '';
                            $inc = 1;
                            foreach ($meter->values as $value){
                                $values .= DateTime::getDateForConsumptionReport($data['current_frame'],
                                        $value->time_point, $data['all_months']).' - '.(float)$value->difference;
                                $values .= ($inc == 4) ? "\n" : '; ';

                                $total_difference += $value->difference;

                                $inc++;

                                if($inc > 4) $inc = 1;
                            }

                            $sheet->row($row, [
                                Addresses::AdrString($meter->address),
                                $values,
                                (float)$total_difference
                            ]);

                            $sheet->setHeight($row, 45);
                            $sheet->setBorder("A{$row}:C{$row}", 'thin');

                            $sheet->cells("A{$row}:C{$row}", function($cells) {
                                $cells->setValignment('center');
                                $cells->setAlignment('center');
                            });
                            $sheet->cells("A{$row}", function($cells) {
                                $cells->setAlignment('left');
                            });

                            $row++;
                        }

                });
            })->setFilename('Потребление - '.Carbon::now()->format('d.m.Y H-i-s'))
                ->download('xlsx');

            die;
        }

        return view('reports.consumption', $data);
    }
}
