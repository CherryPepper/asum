<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;

class StaffController extends Controller
{
    public function getReport(Request $request){
        $data['users'] = User::with(['role'])->TaskReport($request)
            ->where('role_id', '!=', 4)->paginate(12);

        return view('reports.staff', $data);
    }
}
