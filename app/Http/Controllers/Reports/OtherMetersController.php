<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\OMeter;
use App\Models\OValue;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OtherMetersController extends Controller
{
    public function getReport(Request $request){
        $data['meters'] = OMeter::all();
        $data['current_type'] = !empty($request->type_id) ? (int)$request->type_id : 1;
        $data['current_date'] = !empty($request->current_date) ? '20.'.$request->current_date : $this->getOmetersCurrentDate();

        $data['values'] = OValue::with(['meter', 'user.meter.address.parentRecursive'])
            ->where('type_id', $data['current_type'])
            ->where('created_at', '<=', Carbon::parse($data['current_date'])->format('Y-m-d'))
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(12);

        return view('reports.other-meters', $data);
    }
}
