@extends('layouts.user', [
        'title' => 'Отчет по сотрудникам',
        'breadcrumbs' => [
            ['Отчет по сотрудникам'],
        ]
    ])

@section('content')
    <div class="content staff-report">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel">
                        <div class="panel-heading">
                            Отчет по сотрудникам
                        </div>
                        <div class="panel-body">
                            <form action="{{route('report.staff')}}">
                                <div class="row">
                                    <div class="form-wrapper col-sm-2">
                                        <label>Дата от</label>

                                        <div class="form-group">
                                            <div class="input-group date">
                                                <input type="text" class="form-control input-lg" id="date-from" name="date_from" value="{{app('request')->input('date_from')}}">
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-wrapper col-sm-2">
                                        <label>Дата до</label>

                                        <div class="form-group">
                                            <div class="input-group date">
                                                <input type="text" class="form-control input-lg" id="date-to" name="date_to" value="{{app('request')->input('date_to')}}">
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
                                            <a type="submit" class="btn btn-default btn-block input-lg send-form mt-4" href="{{route('report.staff')}}">Очистить</a>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="v-spacing-xs"></div>

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>ФИО</th>
                                        <th>Должность</th>
                                        <th>В процессе</th>
                                        <th>Просроченные</th>
                                        <th>Законченные</th>
                                        <th>Всего</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($users->total()))
                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{$user->first_name.' '.$user->last_name}}</td>
                                            <td>{{$user->role->name}}</td>
                                            <td>{{$user->in_progress}}</td>
                                            <td>{{$user->overdue}}</td>
                                            <td>{{$user->completed}}</td>
                                            <td>{{$user->all_tasks}}</td>
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
                                {{$users->appends($get)->links()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection