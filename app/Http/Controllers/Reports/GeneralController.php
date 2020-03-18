<?php

namespace App\Http\Controllers\Reports;

use App\Helpers\Addresses;
use App\Helpers\DateTime;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Meter;
use App\Models\MetersValue;
use App\Models\Rate;
use App\Models\RateInterval;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Excel;

class GeneralController extends Controller
{
    public function getReport(Request $request){
        $data['date_from'] = !empty($request->date_from) ? $request->date_from : Carbon::today()->subMonth(1)->format('d.m.Y');
        $data['date_to'] = !empty($request->date_to) ? $request->date_to : Carbon::today()->format('d.m.Y');
        $data['rate_id'] = !empty($request->rate_id) ? (int) $request->rate_id : false;
        $multirate = Rate::where('type', 2)->first();
        $data['rate_intervals'] = RateInterval::where('rate_id', $multirate->id)->orderBy('id', 'asc')->get();
        $data['current_rate'] = Rate::where('id', $data['rate_id'])->first();


        $data['addresses'] = $this->getSelectAddresses();
        $data['rates'] = Rate::where('display', 1)->get();

        /** Select meters for report */
        $meters = Meter::with('address.parentRecursive', 'rate')->joinAddress()->checkAccess()
            ->where('status_id', '!=', 3)->where('meters.display', 1)->where('type_id', 1)
            ->orderBy('a.hash', 'asc');

        //if($data['rate_id'])
			$meters->where('rate_id', $data['rate_id']);

        if(isset($request->address['region']) && !empty($request->address['region'])){
            $address_list = Address::where('id', Addresses::getLast($request->address))->first()->path_childs;
            $meters->whereIn('address_id', explode(',', $address_list));
        }

        $data['meters'] = ($request->excel == 1) ? $meters->get() : $meters->paginate(12);

        $collection = ($request->excel == 1) ? $data['meters'] : $data['meters']->getCollection();

        $collection->transform(function ($meter) use($data) {
            $model = new MetersValue();
            $model->setTable('meter_id_'.$meter->id);

            /** Select first and last values for this date range, for every meter */
            $first_value = $model->where('time_point','>=', Carbon::parse($data['date_from'])->format('Y-m-d 00:00:00'))
                ->whereNotNull('value')->orderBy('time_point', 'asc')->first();
            $last_value = $model->where('time_point', '<=', Carbon::parse($data['date_to'])->format('Y-m-d 00:00:00'))
                ->whereNotNull('value')->orderBy('time_point', 'desc')->first();

            $meter->first_value = !empty($first_value) ? $first_value->value : 0;
            $meter->last_value = !empty($last_value) ? $last_value->value : 0;

            $values = [];
            foreach ($data['rate_intervals'] as $interval){

                /** Select all values for current interval */
                $query = $model->select(\DB::raw("SUM(difference) AS difference, SUM(accruals) as accruals"))
                    ->where('time_point', '>', Carbon::parse($data['date_from'])->format('Y-m-d 00:00:00'))
                    ->where('time_point', '<=', Carbon::parse($data['date_to'])->format('Y-m-d 00:00:00'));

                if($interval->time_start < $interval->time_end)
                    $query->where('time_of_day', '>=', $interval->time_start)->where('time_of_day', '<', $interval->time_end);
                else
                    $query->where(function ($q) use ($interval){
                        $q->where('time_of_day', '>=', $interval->time_start)->orWhere('time_of_day', '<', $interval->time_end);
                    });

                $values[$interval->id] = $query->first();
            }

            $meter->values = $values;

            return $meter;
        });

        if($request->excel == 1){
            $file = ($data['current_rate']->type == 1) ? 'template-rate.xlsx' : 'template-multirate.xlsx';

            Excel::load(public_path('excel/full_report/'.$file), function ($template) use($data){
                $template->sheet('report', function ($sheet) use ($data){
                    /** Values date header */
                    $sheet->cell('B1', function ($cell) use ($data){
                        $cell->setValue("Показания на \n {$data['date_from']}");
                    });

                    $sheet->cell('C1', function ($cell) use ($data){
                        $cell->setValue("Показания на \n {$data['date_to']}");
                    });

                    /** Generate Excel for MultiRate */
                    if($data['current_rate']->type == 2){
                        $abc_intervals_arr = ['D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N'];

                        /** Rate intervals Time Header */
                        $i = 0;
                        foreach ($data['rate_intervals'] as $interval){
                            $sheet->setWidth($abc_intervals_arr[$i], 16);

                            $sheet->cell($abc_intervals_arr[$i].'2', function ($cell) use ($data, $interval){
                                $date_time = DateTime::getIntervalHour($interval);

                                $cell->setValue($date_time['hour_start'].'-'.$date_time['hour_end']);
                                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            });

                            $i++;
                        }

                        /** Intervals header */
                        $sheet->cell('D1', function ($cell){
                            $cell->setValue('Потребление КВТ');
                        });
                        $sheet->cells('D1:'.$abc_intervals_arr[$i-1].'1', function($cells) {
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->mergeCells('D1:'.$abc_intervals_arr[$i-1].'1');

                        /** Total difference Header */
                        $sheet->setWidth($abc_intervals_arr[$i], 30);
                        $sheet->setBorder($abc_intervals_arr[$i].'1:'.$abc_intervals_arr[$i].'2', 'thin');
                        $sheet->cell($abc_intervals_arr[$i].'1', function ($cell){
                            $cell->setValue('Итого КВТ');
                        });
                        $i++;

                        /** Total accruals header */
                        $sheet->setWidth($abc_intervals_arr[$i], 30);
                        $sheet->setBorder($abc_intervals_arr[$i].'1:'.$abc_intervals_arr[$i].'2', 'thin');
                        $sheet->cell($abc_intervals_arr[$i].'1', function ($cell){
                            $cell->setValue('Начислено руб');
                        });

                        /** Report Body */
                        $row = 3;
                        foreach ($data['meters'] as $meter){
                            $total_difference = 0;
                            $total_accruals = 0;

                            $row_arr = [
                                Addresses::AdrString($meter->address),
                                $meter->first_value,
                                $meter->last_value
                            ];

                            foreach ($data['rate_intervals'] as $interval){
                                $total_difference += (float) $meter->values[$interval->id]->difference;
                                $total_accruals += (float) $meter->values[$interval->id]->accruals;

                                $row_arr[] = (float) $meter->values[$interval->id]->difference;
                            }
                            $row_arr[] = $total_difference;
                            $row_arr[] = $total_accruals;

                            $sheet->row($row, $row_arr);

                            $sheet->setHeight($row, 40);
                            $sheet->setBorder("A{$row}:{$abc_intervals_arr[$i]}{$row}", 'thin');

                            $row++;
                        }
                    }
                    /** Generate Excel for normal Rate */
                    elseif($data['current_rate']->type == 1){
                        $row = 3;
                        foreach ($data['meters'] as $meter){
                            $sheet->row($row, [
                                Addresses::AdrString($meter->address),
                                $meter->first_value,
                                $meter->last_value,
                                (float) current($meter->values)->difference,
                                (float) current($meter->values)->accruals
                            ]);

                            $sheet->setHeight($row, 40);
                            $sheet->setBorder("A{$row}:E{$row}", 'thin');

                            $row++;
                        }
                    }
                });
            })->setFilename($data['current_rate']->title.' - '.Carbon::now()->format('d.m.Y H-i-s'))
                ->download('xlsx');

            die;
        }

        return view('reports.general', $data);
    }

    public function getTatenergo(Request $request){
        $data['date_from'] = !empty($request->date_from) ? $request->date_from : Carbon::today()->subMonth(1)->format('d.m.Y');
        $data['date_to'] = !empty($request->date_to) ? $request->date_to : Carbon::today()->format('d.m.Y');
        $data['addresses'] = $this->getSelectAddresses();

        /** Select meters for report */
        $meters = Meter::with('address.parentRecursive', 'rate')->joinAddress()->checkAccess()
            ->where('type_id', 2)->where('meters.display', 1)->limit(12);

        if(isset($request->address['region']) && !empty($request->address['region'])){
            $address_list = Address::where('id', Addresses::getLast($request->address))->first()->path_childs;
            $meters->whereIn('address_id', explode(',', $address_list));
        }

        $data['meters'] = $meters->get();

        $data['meters']->transform(function ($meter) use($data) {
            $model = new MetersValue();
            $model->setTable('meter_id_'.$meter->id);

            /** Select first and last values for this date range, for every meter */
            $first_value = $model->where('time_point', '>=', Carbon::parse($data['date_from'])->format('Y-m-d 00:00:00'))
                ->whereNotNull('value')->orderBy('time_point', 'asc')->first();
            $last_value = $model->where('time_point', '<=', Carbon::parse($data['date_to'])->format('Y-m-d 00:00:00'))
                ->whereNotNull('value')->orderBy('time_point', 'desc')->first();

            $meter->first_value = !empty($first_value) ? $first_value->value : 0;
            $meter->last_value = !empty($last_value) ? $last_value->value : 0;

            return $meter;
        });

        Excel::load(public_path('excel/tatenergo/template.xlsx'), function ($template) use($data){

            $template->sheet('report', function ($sheet) use ($data){
                $current_month = DateTime::getMonthByNum($data['date_to']);

                $sheet->cell('A2', function ($cell) use ($data, $current_month){
                    $cell->setValue('от "'.Carbon::parse($data['date_to'])->day.'". '.$current_month.'. '.Carbon::parse($data['date_to'])->year.'г.');
                });

                $sheet->cell('A3', function ($cell) use ($data, $current_month){
                    $cell->setValue('АКТ СНЯТИЯ ПОКАЗАНИЙ расчетных приборов учета за '.$current_month.' месяц  '.Carbon::parse($data['date_to'])->year.' года');
                    $cell->setFontWeight('bold');
                });

                $sheet->cell('A5', function ($cell) use ($data, $current_month){
                    $address = '420111 РФ, Республика Татарстан, г. Казань ';
                    $address .= Addresses::getAdrStringFromRequest();

                    $cell->setValue($address);
                });


                $row = 10;
                foreach ($data['meters'] as $meter){
                    if($row > 10){
                        $sheet->prependRow($row, ['']);
                        $sheet->mergeCells("A{$row}:C{$row}");
                    }


                    $sheet->cell("A{$row}", function ($cell) use ($meter){
                        $cell->setValue($meter->description);
                    });
                    $sheet->cell("D{$row}", function ($cell) use ($meter){
                        $cell->setValue(Addresses::AdrString($meter->address));
                    });
                    $sheet->cell("E{$row}", function ($cell) use ($meter){
                        $cell->setValue('Активная');
                    });
                    $sheet->cell("F{$row}", function ($cell) use ($meter){
                        $cell->setValue('Прямое');
                    });
                    $sheet->cell("G{$row}", function ($cell) use ($meter){
                        $cell->setValue($meter->serial);
                    });
                    $sheet->cell("K{$row}", function ($cell) use ($data){
                        $cell->setValue(Carbon::parse($data['date_from'])->format('d.m.Y'));
                    });
                    $sheet->cell("L{$row}", function ($cell) use ($meter){
                        $cell->setValue($meter->first_value);
                    });
                    $sheet->cell("M{$row}", function ($cell) use ($data){
                        $cell->setValue(Carbon::parse($data['date_to'])->format('d.m.Y'));
                    });
                    $sheet->cell("N{$row}", function ($cell) use ($meter){
                        $cell->setValue($meter->last_value);
                    });

                    $row++;
                }
            });

        })->setFilename('Татэнерго - '.Carbon::now()->format('d.m.Y H-i-s'))
            ->download('xlsx');
    }
}
