@extends('layouts.user', [
        'title' => 'Список заданий',
        'breadcrumbs' => [
            ['Список заданий'],
        ]
    ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel">
                        <div class="panel-heading">
                            Параметры поиска
                        </div>
                        <div class="panel-body">
                            <form action="{{route('tasks.list', ['type' => $type])}}">
                                <div class="row">
                                    <div class="form-wrapper col-sm-4">
                                        <label>Ключевые слова</label>

                                        <div class="form-group">
                                            <input type="text" class="form-control input-lg" name="q" value="{{app('request')->input('q')}}">
                                        </div>
                                    </div>

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
                                </div>
                                <div class="row">
                                    <div class="form-wrapper col-sm-2">
                                        <button type="submit" class="btn btn-info btn-block input-lg send-form mt-4">Применить</button>
                                    </div>
                                    <div class="form-wrapper col-sm-2">
                                        <a type="submit" class="btn btn-default btn-block input-lg send-form mt-4"
                                           href="{{route('tasks.list', ['type' => $type])}}">Очистить</a>
                                    </div>
                                </div>
                            </form>

                            <div class="v-spacing-xs"></div>

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Статус</th>
                                        <th>Описание</th>
                                        <th>Адрес</th>
                                        <th>Сотрудник</th>
                                        <th>Дата начала</th>
                                        <th>Истекает</th>
                                        <th>Действия</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($tasks->total()))
                                        @foreach($tasks as $task)
                                            <tr>
                                                <td>
                                                    @if(($task->date_complete <= $task->date_end) && ($task->date_complete != null))
                                                        <span class="fa fa-circle text-success toggle-block" title="Завершено"></span>
                                                    @elseif(\Carbon\Carbon::now() > $task->date_end)
                                                        <span class="fa fa-circle toggle-block text-danger" title="Истек срок"></span>
                                                    @elseif(\Carbon\Carbon::now() <= $task->date_end)
                                                        <span class="fa fa-circle toggle-block text-warning" title="В процессе"></span>
                                                    @endif
                                                </td>
                                                <td>{{$task->message}}</td>
                                                <td>{{\App\Helpers\Addresses::AdrString($task->address)}}</td>
                                                <td>{{$task->employer->first_name .' '.$task->employer->last_name}}</td>
                                                <td>
                                                    {{\Carbon\Carbon::parse($task->date_start)->format('d.m.Y')}}
                                                </td>
                                                <td>
                                                    {{\Carbon\Carbon::parse($task->date_end)->format('d.m.Y')}}
                                                </td>
                                                <td class="form-actions-icons">
                                                    <div class="actions">
                                                        @if($task->date_complete == null)
                                                        <a href="#" class="mr-10 text-success toggle-block" title="Отметить как выполненное" data-toggle="modal"
                                                           data-target="#modalConfirmation" data-url="{{route('task.setComplete', ['id' => $task->id])}}" data-method="get"
                                                           data-type="success" data-header-text="Подтверждение действия"
                                                           data-confirm-text="Вы действительно хотите завершить задание?">
                                                            <span class="fa fa-check-circle-o" aria-hidden="true"></span>
                                                        </a>
                                                        @endif

                                                        @if($userInfo->role->slug != 'technician')
                                                        <a href="#" class="text-danger toggle-block" title="Удалить задание" data-toggle="modal" data-target="#modalConfirmation"
                                                           data-url="{{route('task.delete', ['id' => $task->id])}}" data-method="get"
                                                           data-type="danger" data-header-text="Подтверждение действия"
                                                           data-confirm-text="Вы действительно хотите удалить задание?">
                                                            <span class="fa fa-trash-o" aria-hidden="true"></span>
                                                        </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7">
                                                <p class="text-center mt-100 mb-100">Не найдено ни одного задания</p>
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>

                            <div class="pull-right">
                                <?php $get = $_GET; unset($get['page'])?>
                                {{$tasks->appends($get)->links()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection