@php
    $breadcrumbs[] = ['Отчет по потерям', route('report.loss')];

    if(!empty($parents)){
        foreach ($parents as $parent)
            $breadcrumbs[] = [$parent->serial, route('report.loss.points', ['id' => $parent->id])];

        $breadcrumbs[] = ['Точки'];
    }
@endphp

@extends('layouts.user', [
        'title' => 'Отчет по потерям',
        'breadcrumbs' => $breadcrumbs
    ])

@section('content')
    <div class="content loss-report">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel">
                        <div class="panel-heading">
                            Отчет по потерям
                        </div>
                        <div class="panel-body">
                            <form action="{{route('report.loss.points', ['id' => $parent_id])}}">
                                <div class="col-sm-11">
                                    <div class="row">
                                        <div class="form-wrapper col-sm-3">
                                            <label>Дата от</label>

                                            <div class="form-group">
                                                <div class="input-group date">
                                                    <input type="text" class="form-control input-lg" id="datetime-from" name="date_from" value="{{$date_from}}">
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
                                                    <input type="text" class="form-control input-lg" id="datetime-to" name="date_to" value="{{$date_to}}">
                                                    <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-wrapper col-sm-2">
                                                <label></label>
                                                <button type="submit" class="btn btn-info btn-block input-lg send-form mt-4">Применить</button>
                                            </div>
                                            <div class="form-wrapper col-sm-2">
                                                <label></label>
                                                <a type="submit" class="btn btn-default btn-block input-lg send-form mt-4" href="{{route('report.loss.points', ['id' => $parent_id])}}">Очистить</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="v-spacing-xs"></div>

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover report">
                                    <thead>
                                    <tr>
                                        <th>
                                            Время
                                            <span class="badge" data-toggle="tooltip" data-placement="top" title="Всего точек в заданном интервале"> {{$points->total()}} </span>
                                        </th>
                                        <th>
                                            Потребление
                                            <span class="badge" data-toggle="tooltip" data-placement="top" title="Общее потребление в заданном интервале"> {{$stats->sum_difference}} </span>
                                        </th>
                                        <th>
                                            ID запроса
                                            <span class="badge" data-toggle="tooltip" data-placement="top" title="Всего неопрошенных точек"> {{$stats->without_request}} </span>
                                        </th>
                                        <th>
                                            Ошибка
                                            <span class="badge" data-toggle="tooltip" data-placement="top" title="Всего запросов завершенных с ошибкой"> {{$stats->with_error}} </span>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($points->total()))
                                        @foreach($points as $point)
                                            <tr>
                                                <td class="text-center">{{\Carbon\Carbon::parse($point->time_point)->format('d.m.Y H:i')}}</td>
                                                <td>{{$point->difference}}</td>
                                                <td>{{($point->query_id == null) ? '-' :  $point->query_id}}</td>
                                                <td>{{($point->error_code == null) ? '-' :  $point->error_code}}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7">
                                                <p class="text-center mt-100 mb-100">Не найдено ни одной точки</p>
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>

                            <div class="pull-right">
                                @php $get = $_GET; unset($get['page']) @endphp
                                {{$points->appends($get)->links()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection