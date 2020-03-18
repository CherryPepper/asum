@extends('layouts.user', [
        'title' => 'Список сотрудников',
        'breadcrumbs' => [
            ['Список сотрудников'],
        ]
    ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel">
                        <div class="panel-heading">
                            Параметры отображения
                        </div>
                        <div class="panel-body">
                            <form action="{{route('employer.list')}}">
                                <div class="row">
                                    <div class="form-wrapper col-sm-4">
                                        <label>Должность</label>
                                        <div class="form-group">
                                            <select class="form-control input-lg custom-select" name="role_id">
                                                <option>Все</option>
                                                @foreach($roles as $role)
                                                    <option value="{{$role->id}}" {{app('request')->input('role_id') == $role->id ? 'selected' : ''}}>
                                                        {{$role->name}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-wrapper col-sm-2">
                                        <label></label>
                                        <button type="submit" class="btn btn-info btn-block input-lg send-form mt-4">Применить</button>
                                    </div>
                                    <div class="form-wrapper col-sm-2">
                                        <label></label>
                                        <a type="submit" class="btn btn-default btn-block input-lg send-form mt-4"
                                           href="{{route('employer.list')}}">Очистить</a>
                                    </div>
                                </div>
                            </form>

                            <div class="v-spacing-xs"></div>

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Имя</th>
                                        <th>E-mail</th>
                                        <th>Логин</th>
                                        <th>Должность</th>
                                        <th>Дата добавления</th>
                                        <th>Действия</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($employers->total()))
                                        @foreach($employers as $employer)
                                            <tr>
                                                <td>{{$employer->first_name.' '.$employer->last_name}}</td>
                                                <td>{{$employer->email}}</td>
                                                <td>{{$employer->login}}</td>
                                                <td>{{$employer->role->name}}</td>
                                                <td>{{\Carbon\Carbon::parse($employer->created_at)->format('d.m.Y')}}</td>
                                                <td  class="form-actions-icons">
                                                    <div class="actions">
                                                        <a href="{{route('employer.edit', ['id' => $employer->id])}}" class="mr-10 text-info toggle-block" title="Редактирование сотрудника">
                                                            <span class="fa fa-pencil-square-o" aria-hidden="true"></span>
                                                        </a>

                                                        <a href="#" class="text-danger toggle-block" title="Удалить сотрудника" data-toggle="modal" data-target="#modalConfirmation"
                                                           data-url="{{route('employer.delete', ['id' => $employer->id])}}" data-method="get"
                                                           data-type="danger" data-header-text="Подтверждение действия"
                                                           data-confirm-text="Вы действительно хотите удалить сотрудника?">
                                                            <span class="fa fa-trash-o" aria-hidden="true"></span>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6">
                                                <p class="text-center mt-100 mb-100">Не найдено ни одного сотрудника</p>
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>

                            <div class="pull-right">
                                <?php $get = $_GET; unset($get['page'])?>
                                {{$employers->appends($get)->links()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection