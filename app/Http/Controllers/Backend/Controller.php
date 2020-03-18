<?php

namespace App\Http\Controllers\Backend;

use Carbon\Carbon;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    protected $time, $query_id, $param, $value, $serial, $pt,
               $error, $request, $operator, $http_request, $operator_id,
               $current_day, $current_point, $point_interval = 900;

    public function __construct(Request $request){
        $this->time = Carbon::now();
        $this->current_day = Carbon::today();
        $this->current_point = Carbon::parse(date('Y-m-d H:i:s', floor((time()-strtotime($this->current_day))
                /$this->point_interval)*$this->point_interval+strtotime($this->current_day)));
        $this->query_id = (int)$request->input('queryid');
        $this->param = $request->input('param');
        $this->value = !empty($request->input('value')) ? $request->input('value'): $request->input('vu');
        $this->serial = !empty($request->input('serial')) ? $request->input('serial') : $request->input('sn');
        $this->pt = $request->input('pt');
        $this->error = !empty($request->input('error')) ? $request->input('error') : $request->input('er');
        $this->http_request = $request;

        if(!empty($this->value))
            $this->value = round($this->value, 2);
        if(!empty($this->error)){
            $this->param = 'error';
            $this->value = $this->error;
        }

        $this->operator = $request->route('operator');
        $this->request = $request->route('request');
    }
}
