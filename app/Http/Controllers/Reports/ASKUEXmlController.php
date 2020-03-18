<?php

namespace App\Http\Controllers\Reports;

use App\Helpers\Addresses;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;
use App\Models\Counter;
use App\Models\Meter;
use App\Models\MetersValue;
use App\Models\Rate;
use App\Models\RateInterval;
use Carbon\Carbon;

class ASKUEXmlController extends Controller
{
    public function getXml(Request $request){
        $data['current_date'] = Carbon::today();
        $data['date_from'] = $request->date_from ?? Carbon::today()->subMonth(1)->format('d.m.Y');
        $data['date_to'] = $request->date_to ?? Carbon::today()->format('d.m.Y');
        $data['rate_id'] = $request->rate_id ?? Rate::first()->id;
        $data['rate_intervals'] = RateInterval::where('rate_id', $data['rate_id'])->orderBy('id', 'asc')->get();
        $data['current_rate'] = Rate::where('id', $data['rate_id'])->first();

        $data['addresses'] = $this->getSelectAddresses();
        $data['rates'] = Rate::where('display', 1)->get();

        /** Select meters for report */
        $meters = Meter::with(['address.parentRecursive', 'rate', 'user'])->joinAddress()->checkAccess()
            ->where('status_id', '!=', 3)->where('rate_id', $data['rate_id'])->where('meters.display', 1)->where('type_id', 1)
            ->orderBy('a.hash', 'asc');

        if(isset($request->address['region']) && !empty($request->address['region'])){

            $address_list = Address::where('id', Addresses::getLast($request->address))->first()->path_childs;
            $meters->whereIn('address_id', explode(',', $address_list));
        }

        $data['meters'] = $meters->get();

        $data['meters']->transform(function ($meter) use($data) {
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

        $data = $this->getView($data);
        $headers = $this->getHeaders($data);

        return \Response::make($data['view'], 200, $headers);
    }

    private function getView($data){
        $num = Counter::getNum('askue-xml');
        $date = Carbon::today()->format('d_m_Y');

        return [
            'file' => "ПУ_ {$date}_12_{$num}.xml",
            'view' => \View::make('reports.askue-xml', $data)->render()
        ];
    }

    private function getHeaders($data){
        return [
            'Content-type' => 'text/xml',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $data['file']),
            'Content-Length' => mb_strlen($data['view'], 'windows-1251')
        ];
    }
}
