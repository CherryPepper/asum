<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;

class Bootstrap extends Controller
{
    public function index(Request $request){
        /** Controllers init  */

        new ScheduleController($request);
        new QueryController($request);
        new ErrorController($request);
        new ResponseController($request);
    }
}
