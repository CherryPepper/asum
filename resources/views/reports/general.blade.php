@php $get = $_GET; unset($get['page']) @endphp

@extends('layouts.user', [
        'title' => 'Развернутый отчет',
        'breadcrumbs' => [
            ['Развернутый отчет'],
        ]
    ])

@section('content')
    <div class="content general-report">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel">
                        <div class="panel-heading">
                            Развернутый отчет
                        </div>
                        <div class="panel-body">
                            <form action="{{route('report.general')}}">
                                <div class="col-sm-5">
                                    <div class="row">
                                        <div class="form-wrapper col-sm-6">
                                            <label>Район</label>

                                            <div class="form-group">
                                                <select class="form-control input-lg address-search" name="address[region]">
                                                    @if(!(int)app('request')->input('address.region') && (app('request')->input('address.region') !== null))
                                                        <option value="{{app('request')->input('address.region')}}">{{app('request')->input('address.region')}}</option>
                                                    @else
                                                        <option></option>
                                                        @foreach($addresses['regions'] as $region)
                                                            <option value="{{$region->id}}" {{app('request')->input('address.region') == $region->id ? 'selected' : ''}}>
                                                                {{$region->name}}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-wrapper col-sm-6">
                                            <label>Улица</label>

                                            <div class="form-group">
                                                <select class="form-control input-lg address-search" name="address[street]" {{app('request')->input('address.street') ? '' : 'disabled'}}>
                                                    @if((int)app('request')->input('address.street'))
                                                        <option></option>
                                                        @foreach($addresses['streets'] as $region)
                                                            <option value="{{$region->id}}" {{app('request')->input('address.street') == $region->id ? 'selected' : ''}}>
                                                                {{$region->name}}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-wrapper col-sm-3">
                                            <label>Дом</label>

                                            <div class="form-group">
                                                <select class="form-control input-lg address-search" name="address[home]" {{app('request')->input('address.home') ? '' : 'disabled'}}>
                                                    @if((int)app('request')->input('address.home'))
                                                        <option></option>
                                                        @foreach($addresses['homes'] as $region)
                                                            <option value="{{$region->id}}" {{app('request')->input('address.home') == $region->id ? 'selected' : ''}}>
                                                                {{$region->name}}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-wrapper col-sm-3">
                                            <label>Квартира</label>

                                            <div class="form-group">
                                                <select class="form-control input-lg address-search" name="address[apartment]" {{app('request')->input('address.apartment') ? '' : 'disabled'}}>
                                                    @if((int)app('request')->input('address.apartment'))
                                                        <option></option>
                                                        @foreach($addresses['apartments'] as $region)
                                                            <option value="{{$region->id}}" {{app('request')->input('address.apartment') == $region->id ? 'selected' : ''}}>
                                                                {{$region->name}}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-7">
                                    <div class="row">
                                        <div class="form-wrapper col-sm-4">
                                            <label>Тариф</label>

                                            <div class="form-group">
                                                <select class="form-control input-lg custom-select" name="rate_id">
													<option></option>
                                                    @foreach($rates as $rate)
                                                        <option value="{{$rate->id}}" {{$rate_id == $rate->id ? 'selected' : ''}}>
                                                            {{$rate->title}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-wrapper col-sm-4">
                                            <label>Дата от</label>

                                            <div class="form-group">
                                                <div class="input-group date">
                                                    <input type="text" class="form-control input-lg" id="date-from" name="date_from" value="{{$date_from}}" autocomplete="0">
                                                    <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-wrapper col-sm-4">
                                            <label>Дата до</label>

                                            <div class="form-group">
                                                <div class="input-group date">
                                                    <input type="text" class="form-control input-lg" id="date-to" name="date_to" value="{{$date_to}}" autocomplete="0">
                                                    <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-wrapper col-sm-4">
                                            <label></label>
                                            <button type="submit" class="btn btn-info btn-block input-lg send-form mt-4">Применить</button>
                                        </div>
                                        <div class="form-wrapper col-sm-4">
                                            <label></label>
                                            <a type="submit" class="btn btn-default btn-block input-lg send-form mt-4" href="{{route('report.general')}}">Очистить</a>
                                        </div>
                                    </div>
                                </div>

                            </form>

                            <div class="v-spacing-xs"></div>

                            <p class="download-excel">
                                <a href="{{route('report.askue_xml')}}?{{http_build_query(app('request')->input())}}">
                                    <i class="fa fa-file-text"></i>
                                    АСКУЭ xml</a>
                                <a href="{{route('report.general', ['excel' => 1])}}&{{http_build_query(app('request')->input())}}">
                                    <i class="fa fa-file-excel-o"></i>
                                    Скачать в Excel</a>
                                <a href="{{route('report.tatenergo')}}?{{http_build_query(app('request')->input())}}">
                                    <i class="fa fa-file-excel-o"></i>
                                    Отчет татэнерго</a>
                            </p>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover report">
                                    <thead>
                                    <tr>
                                        <th>Адрес</th>
                                        <th>Показания на <br> {{$date_from}}</th>
                                        <th>Показания на <br> {{$date_to}}</th>
                                        @if(isset($current_rate->type) && ($current_rate->type == 1))
											<th class="no-padding" colspan="1">
												<p class="pt-15 pb-15 mb-0 color-red">Потреблено КВТ <br> (Однотариф)</p>
												<table class="table">
													<thead>
													<tr>
														<th class="w130">00:00-24:00</th>
													</tr>
													</thead>
												</table>
											</th>
                                        @elseif(isset($current_rate->type) && ($current_rate->type == 2))
                                            <th class="no-padding" colspan="{{sizeof($rate_intervals)}}">
                                                <p class="pt-15 pb-15 mb-0 color-red">Потреблено КВТ <br> (Мультитариф)</p>
                                                <table class="table">
                                                    <thead>
                                                    <tr>
                                                    @foreach($rate_intervals as $interval)
                                                        @php $date_time = \App\Helpers\DateTime::getIntervalHour($interval) @endphp
                                                        <th class="w130">{{$date_time['hour_start'].'-'.$date_time['hour_end']}}</th>
                                                    @endforeach
                                                    </tr>
                                                    </thead>
                                                </table>
                                            </th>
                                            <th>
                                                Итого КВТ
                                            </th>
										@else
											<th class="no-padding" colspan="1">
												<p class="pt-15 pb-15 mb-0 color-red">Потреблено КВТ <br> (Однотариф)</p>
												<table class="table">
													<thead>
													<tr>
														<th class="w130">00:00-24:00</th>
													</tr>
													</thead>
												</table>
											</th>
											<th class="no-padding" colspan="{{sizeof($rate_intervals)}}">
												<p class="pt-15 pb-15 mb-0 color-red">Потреблено КВТ <br> (Мультитариф)</p>
												<table class="table">
													<thead>
													<tr>
														@foreach($rate_intervals as $interval)
															@php $date_time = \App\Helpers\DateTime::getIntervalHour($interval) @endphp
															<th class="w130">{{$date_time['hour_start'].'-'.$date_time['hour_end']}}</th>
														@endforeach
													</tr>
													</thead>
												</table>
											</th>
											<th>
												Итого КВТ
											</th>
                                        @endif
                                        <th>Начислено руб</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @if(!empty($meters->total()))
                                        @foreach($meters as $meter)
                                            @php
                                                $total_difference = 0;
                                                $total_accruals = 0;
                                            @endphp
                                            <tr>
                                                <td>{{\App\Helpers\Addresses::AdrString($meter->address)}}</td>
                                                <td>{{(float) $meter->first_value}}</td>
                                                <td>{{(float) $meter->last_value}}</td>
                                                @if($current_rate->type == 1)
                                                    <td>{{(float) current($meter->values)->difference}}</td>
                                                    <td>{{(float) current($meter->values)->accruals}}</td>
                                                @else
                                                    @foreach($rate_intervals as $interval)
                                                        @php
                                                            $total_difference += (float) $meter->values[$interval->id]->difference;
                                                            $total_accruals += (float) $meter->values[$interval->id]->accruals;
                                                        @endphp
                                                        <td class="w130">{{(float) $meter->values[$interval->id]->difference}}</td>
                                                    @endforeach

                                                    <td>{{$total_difference}}</td>
                                                    <td>{{$total_accruals}}</td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6">
                                                <p class="text-center mt-100 mb-100">Не найдено ни одной записи</p>
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>

                            <div class="pull-right">
                                {{$meters->appends($get)->links()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection