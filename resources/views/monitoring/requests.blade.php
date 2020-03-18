@php $get = $_GET; unset($get['page']) @endphp

@extends('layouts.user', [
        'title' => 'Мониторинг запросов',
        'breadcrumbs' => [
            ['Мониторинг запросов'],
        ]
    ])

@section('content')
    <div class="content monitoring-requests">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel">
                        <div class="panel-heading">
                            Мониторинг запросов
                        </div>
                        <div class="panel-body">
                            @if((int)$action_id > 0)
                            <div class="mb-30">
                                <form action="{{route('monitoring.requests')}}">
                                    <input type="hidden" name="action_id" value="{{$action_id}}">
                                    <input type="hidden" name="status" value="{{$status}}">

                                    <div class="col-sm-6">
                                        <div class="form-wrapper col-sm-6">
                                            <label>Район</label>

                                            <div class="form-group">
                                                <select class="form-control input-lg address-search" name="address[region]">
                                                    @if(!(int)@$get['address']['region'] && (@$get['address']['region'] !== null))
                                                        <option value="{{@$get['address']['region']}}">{{@$get['address']['region']}}</option>
                                                    @else
                                                        <option></option>
                                                        @foreach($addresses['regions'] as $region)
                                                            <option value="{{$region->id}}" {{@$get['address']['region'] == $region->id ? 'selected' : ''}}>
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
                                                <select class="form-control input-lg address-search" name="address[street]" {{@$get['address']['street'] ? '' : 'disabled'}}>
                                                    @if((int)@$get['address']['street'])
                                                        <option></option>
                                                        @foreach($addresses['streets'] as $region)
                                                            <option value="{{$region->id}}" {{@$get['address']['street'] == $region->id ? 'selected' : ''}}>
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
                                                <select class="form-control input-lg address-search" name="address[home]" {{@$get['address']['home'] ? '' : 'disabled'}}>
                                                    @if((int)@$get['address']['home'])
                                                        <option></option>
                                                        @foreach($addresses['homes'] as $region)
                                                            <option value="{{$region->id}}" {{@$get['address']['home'] == $region->id ? 'selected' : ''}}>
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
                                                <select class="form-control input-lg address-search" name="address[apartment]" {{@$get['address']['apartment'] ? '' : 'disabled'}}>
                                                    @if((int)@$get['address']['apartment'])
                                                        <option></option>
                                                        @foreach($addresses['apartments'] as $region)
                                                            <option value="{{$region->id}}" {{@$get['address']['apartment'] == $region->id ? 'selected' : ''}}>
                                                                {{$region->name}}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-wrapper col-sm-3">
                                            <label>Дата от</label>

                                            <div class="form-group">
                                                <div class="input-group date">
                                                    <input type="text" class="form-control input-lg" id="date-from" name="date_from" value="{{@$get['date_from']}}">
                                                    <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-wrapper col-sm-3">
                                            <label>Дата до</label>

                                            <div class="form-group">
                                                <div class="input-group date">
                                                    <input type="text" class="form-control input-lg" id="date-to" name="date_to" value="{{@$get['date_to']}}">
                                                    <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-wrapper col-sm-3">
                                            <label></label>
                                            <button type="submit" class="btn btn-info btn-block input-lg send-form mt-4">Применить</button>
                                        </div>

                                        <div class="form-wrapper col-sm-3">
                                            <label></label>
                                            <a type="submit" class="btn btn-default btn-block input-lg send-form mt-4" href="{{route('monitoring.requests', ['action_id' => $action_id])}}">Очистить</a>
                                        </div>
                                    </div>

                                    <div class="clear"></div>
                                </form>
                            </div>
                            @endif

                            @foreach($actions as $action)
                                <div class="actions @if($action->id == $action_id) active @endif">
                                    <a href="{{route('monitoring.requests', ['action_id' => $action->id])}}">
                                        {{$action->description}}({{$action->name}})
                                    </a>
                                </div>

                                @if($action->id == $action_id)
                                    <div class="queries">
                                        <div class="status">
                                            <a href="{{route('monitoring.requests', ['action_id' => $action->id])}}"
                                                @if($status == false) class="active" @endif>
                                                Все
                                            </a>
                                            <a href="{{route('monitoring.requests', ['action_id' => $action->id, 'status' => 1])}}"
                                               @if($status == 1) class="active" @endif>
                                                <span class="fa fa-circle text-success"></span>
                                                Выполненные
                                            </a>
                                            <a href="{{route('monitoring.requests', ['action_id' => $action->id, 'status' => 2])}}"
                                               @if($status == 2) class="active" @endif>
                                                <span class="fa fa-circle text-warning"></span>
                                                В ожидании
                                            </a>
                                            <a href="{{route('monitoring.requests', ['action_id' => $action->id, 'status' => 3])}}"
                                               @if($status == 3) class="active" @endif>
                                                <span class="fa fa-circle text-danger"></span>
                                                С ошибкой
                                            </a>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                <tr>
                                                    <th>Запрос</th>
                                                    <th>Ответ</th>
                                                    <th>Дата и статус</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if(!empty($queries->total()))
                                                @foreach($queries as $query)
                                                    <tr>
                                                        <td>{{@urldecode(http_build_query(json_decode($query->request)))}}</td>
                                                        <td>{{@urldecode(http_build_query(json_decode($query->response)))}}</td>
                                                        <td>
                                                            @if(!empty($query->code_error))
                                                                <span class="fa fa-circle text-danger"></span>
                                                            @elseif(empty($query->completed_at))
                                                                <span class="fa fa-circle text-warning"></span>
                                                            @elseif(empty($query->code_error) && !empty($query->completed_at))
                                                                <span class="fa fa-circle text-success"></span>
                                                            @endif
                                                            {{\Carbon\Carbon::parse($query->completed_at)->format('H:i:s d.m.Y')}}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="3">
                                                            <p class="text-center mt-100 mb-100">Не найдено ни одного запроса</p>
                                                        </td>
                                                    </tr>
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="pull-right">
                                            {{$queries->appends($get)->links()}}
                                        </div>

                                        <div class="clear"></div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection