<?php

namespace App\Http\Controllers;

use App\Models\Meter;

class SelectChildsController extends Controller
{
    public function getMeters(){
        $data['meters'] = Meter::with(['address.parentRecursive', 'status', 'user', 'rate'])->joinAddress()->checkAccess()
            ->where('meters.display', 1)
            ->whereNotIn('status_id', [3,4])
            ->where('meters.parent_id', 0)
            ->orderBy('type_id', 'desc')
            ->orderBy('serial', 'asc')
            ->get();

        return view('meters.select-childs', $data);
    }
}
