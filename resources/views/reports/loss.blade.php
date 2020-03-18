@php
    $breadcrumbs[] = ['Отчет по потерям', route('report.loss')];

    if(!empty($parents)){
        foreach ($parents as $parent)
            $breadcrumbs[] = [$parent->serial, route('report.loss', ['id' => $parent->id])];

        $breadcrumbs[] = ['Контрольные счетчики'];
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
                            <form action="{{route('report.loss', ['id' => $parent_id])}}">
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
                                                <a type="submit" class="btn btn-default btn-block input-lg send-form mt-4" href="{{route('report.loss', ['id' => $parent_id])}}">Очистить</a>
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
                                        <th>Серийный номер</th>
                                        <th>
                                            Всего дочерних
                                            <span class="badge" data-toggle="tooltip" data-placement="top" title="Количество дочерних пользовательских счетчиков"> ? </span>
                                        </th>
                                        <th>
                                            Не опрошенных
                                            <span class="badge" data-toggle="tooltip" data-placement="top" title="Количество не опрошенных точек в заданном интервале. Контрольный счетчик / Дочерние счетчики"> ? </span>
                                        </th>
                                        <th>
                                            С ошибкой
                                            <span class="badge" data-toggle="tooltip" data-placement="top" title="Количество точек в заданном интервале вернувших ошибку. Контрольный счетчик / Дочерние счетчики"> ? </span>
                                        </th>
                                        <th>
                                            Потребление КВТ
                                            <span class="badge" data-toggle="tooltip" data-placement="top" title="Потребление энергии в заданном интервале. Контрольный счетчик / Дочерние счетчики"> ? </span>
                                        </th>
                                        <th>Потери КВТ</th>
                                        <th>Действия</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($meters->total()))
                                        @foreach($meters as $meter)
                                            <tr>
                                                <td class="text-center">{{$meter->serial}}</td>
                                                <td>{{$meter->childs_cnt}}</td>
                                                <td>{{$meter->without_request}} / {{$meter->childs->without_request}}</td>
                                                <td>{{$meter->with_error}} / {{$meter->childs->with_error}}</td>
                                                <td>{{(float)$meter->sum_difference}} / {{(float)$meter->childs->sum_difference}}</td>
                                                <td>{{$meter->sum_difference-$meter->childs->sum_difference}}</td>
                                                <td class="actions">
                                                    <a href="{{route('report.loss', ['id' => $meter->id, 'date_from' => $date_from, 'date_to' => $date_to])}}"
                                                       data-toggle="tooltip" data-placement="left" title="Дочерние контрольные счетчики" class="mr-5">
                                                        <span class="fa fa-tachometer"></span></a>

                                                    <a href="{{route('report.loss.childs', ['id' => $meter->id, 'date_from' => $date_from, 'date_to' => $date_to])}}" data-toggle="tooltip" data-placement="left" title="Дочерние пользовательские счетчики" class="mr-5 text-success">
                                                        <span class="fa fa-sitemap"></span></a>

                                                    <a href="{{route('report.loss.points', ['id' => $meter->id, 'date_from' => $date_from, 'date_to' => $date_to])}}" data-toggle="tooltip" data-placement="left" title="Точки в заданном интервале" class="text-warning">
                                                        <span class="glyphicon glyphicon-list"></span></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7">
                                                <p class="text-center mt-100 mb-100">Не найдено ни одного контролького счетчика</p>
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