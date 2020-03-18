@extends('layouts.user', [
        'title' => 'Потребление энергии',
        'breadcrumbs' => [
            ['Потребление энергии'],
        ]
    ])

@section('content')
    <div class="content consumption-report">
        <input type="hidden" id="frameId" value="{{$current_frame}}">

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel">
                        <div class="panel-heading">
                            Потребление энергии
                        </div>
                        <div class="panel-body">
                            <form action="{{route('report.consumption')}}">
                                <div class="col-sm-4">
                                    <div class="row">
                                        <div class="form-wrapper col-sm-12">
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

                                        <div class="form-wrapper col-sm-12">
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

                                        <div class="form-wrapper col-sm-6">
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
                                        <div class="form-wrapper col-sm-6">
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

                                <div class="col-sm-4">
                                    <div class="row">
                                        <div class="form-wrapper col-sm-12">
                                            <label>Период</label>

                                            <div class="form-group">
                                                <select class="form-control input-lg custom-select frame" name="frame">
                                                    @foreach($frames as $id=>$frame)
                                                        <option value="{{$id}}" {{app('request')->input('frame') == $id ? 'selected' : ''}}>
                                                            {{$frame}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-wrapper col-sm-6">
                                            <label>Дата от</label>

                                            <div class="form-group">
                                                <div class="input-group date">
                                                    <input type="text" class="form-control input-lg" id="start-date" name="date_from" value="{{$date_from}}">
                                                    <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-wrapper col-sm-6">
                                            <label>Дата до</label>

                                            <div class="form-group">
                                                <div class="input-group date">
                                                    <input type="text" class="form-control input-lg" id="end-date" name="date_to" value="{{$date_to}}">
                                                    <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-wrapper col-sm-6 time-picker">
                                            <label>Время от</label>

                                            <div class="form-group">
                                                <div class="input-group date">
                                                    <input type="text" class="form-control input-lg" id="time-from" name="time_from" value="{{app('request')->input('time_from')}}">
                                                    <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-wrapper col-sm-6 time-picker">
                                            <label>Время до</label>

                                            <div class="form-group">
                                                <div class="input-group date">
                                                    <input type="text" class="form-control input-lg" id="time-to" name="time_to" value="{{app('request')->input('time_to')}}">
                                                    <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-2">
                                    <div class="row">
                                        <div class="form-wrapper col-sm-12">
                                            <div class="form-group">
                                                <label></label>
                                                <a type="submit" class="btn btn-default btn-block input-lg send-form mt-4" href="{{route('report.consumption')}}">Очистить</a>
                                            </div>
                                        </div>

                                        <div class="form-wrapper col-sm-12">
                                            <div class="form-group">
                                                <label></label>
                                                <button type="submit" class="btn btn-info btn-block input-lg send-form mt-4">Применить</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="clear"></div>

                            <p class="download-excel">
                                <a href="{{route('report.consumption', ['excel' => 1])}}&{{http_build_query(app('request')->input())}}">
                                    <i class="fa fa-file-excel-o"></i>
                                    Скачать в Excel</a>
                            </p>

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover report">
                                    <thead>
                                    <tr>
                                        <th class="text-center" style="width: 25%">Адрес</th>
                                        <th class="text-center">Потребление КВТ</th>
                                        <th class="text-center" style="width: 10%">Всего КВТ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($meters->total()))
                                        @foreach($meters as $meter)
                                            <tr>
                                                <td>{{\App\Helpers\Addresses::AdrString($meter->address)}}</td>
                                                <td>
                                                    @php $total_difference = 0; @endphp
                                                    @foreach($meter->values as $value)
                                                        <div class="col-sm-3">
                                                            <strong>{{\App\Helpers\DateTime::getDateForConsumptionReport($current_frame, $value->time_point, $all_months)}}</strong> -
                                                            {{(float)$value->difference}}
                                                        </div>

                                                        @php $total_difference += $value->difference; @endphp
                                                    @endforeach
                                                </td>
                                                <td class="text-center">
                                                    {{(float)$total_difference}}
                                                </td>
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
                                @php $get = $_GET; unset($get['page']) @endphp
                                {{$meters->appends($get)->links()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection