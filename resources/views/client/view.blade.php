@extends('layouts.user', [
        'title' => 'Данные абонента: '.$user->first_name.' '.$user->last_name,
        'breadcrumbs' => [
            ['Поиск абонента', route('client.list')],
            [$user->first_name.' '.$user->last_name]
        ]
    ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <div class="panel">
                        <div class="panel-heading">
                            Данные абонента
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive user-info">
                                <table class="table table-sm">
                                    <tbody>
                                    <tr>
                                        <td class="bg-gray">Договор</td>
                                        <td>№ {{$user->contract}}</td>

                                        <td class="bg-gray">Статус счетчика</td>
                                        <td>{{$user->meter->status->name}}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray">Имя</td>
                                        <td>{{$user->first_name. ' '. $user->last_name}}</td>

                                        <td class="bg-gray">Серийный номер</td>
                                        <td>{{$user->meter->serial}}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray">E-mail</td>
                                        <td>{{$user->email}}</td>

                                        <td class="bg-gray">IP адрес</td>
                                        <td>{{$user->meter->ip_address}}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray">Адрес</td>
                                        <td>{{\App\Helpers\Addresses::AdrString($user->meter->address)}}</td>

                                        <td class="bg-gray">Тариф</td>
                                        <td>{{$user->meter->rate->title}}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray">Паспорт</td>
                                        <td>{{$user->passport.' - '.$user->passport_mvd}}</td>

                                        <td class="bg-gray">Текущее показание</td>
                                        <td id="refresh-meter-value">
                                            <span id="last-value">{{$user->meter->value}}</span> Квт &nbsp;

                                            <a href="/meter_instruction/refresh/{{$user->meter->id}}" title="Обновить показание" id="refresh-btn">
                                                <i class="fa fa-refresh"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray">Баланс</td>
                                        <td>0 руб</td>

                                        <td class="bg-gray"></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray">Дата добавления</td>
                                        <td>{{\Carbon\Carbon::parse($user->created_at)->format('d.m.Y')}}</td>

                                        <td class="bg-gray"></td>
                                        <td></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-5">
                    <div class="panel">
                        <div class="panel-heading">
                            Действия
                        </div>
                        <div class="panel-body">
                            <a class="btn btn-block btn-outline btn-info open-popup" title="Показания счетчика"
                               href="{{route('meter.history', ['id' => $user->meter->id])}}?frame=year">
                                <i class="fa fa-bar-chart-o"></i>
                                История показаний
                            </a>
                            <a class="btn btn-block btn-outline btn-warning open-popup" title="Добавление показания"
                                href="{{route('ometers.add_value', ['user_id' => $user->id])}}">
                                <i class="fa fa-plus"></i>
                                Добавить показания
                            </a>

                            <div class="col-xs-12" style="height:88px;"></div>

                            @if($user->display == 1)
                                <a class="btn btn-block btn-info" href="{{route('client.edit', ['id' => $user->id])}}">
                                    <i class="fa  fa-pencil-square-o"></i> Редактирование абонента
                                </a>
                                <a class="btn btn-block btn-danger" data-toggle="modal"
                                   data-target="#modalConfirmation" data-url="{{route('client.delete.post')}}"
                                   data-id="{{$user->id}}" data-method="delete-confirm"  data-type="danger"
                                   data-header-text="Вы действительно хотите удалить данного абонента?"
                                   data-confirm-text="Подтвердите свой пароль для продолжения">
                                    <i class="fa fa-trash-o"></i> Удаление абонента
                                </a>
                            @else
                                <a class="btn btn-block btn-success" data-toggle="modal"
                                   data-target="#modalConfirmation" data-url="{{route('client.recover', ['id' => $user->id])}}"
                                   data-id="{{$user->id}}" data-method="get"  data-type="success"
                                   data-header-text="Подтверждение действия"
                                   data-confirm-text="Вы действительно хотите восстановить абонента?">
                                    <i class="fa fa-refresh"></i> Восстановление абонента
                                </a>
                                <a class="btn btn-block btn-danger" data-toggle="modal"
                                   data-target="#modalConfirmation" data-url="{{route('client.delete.permanently.post')}}"
                                   data-id="{{$user->id}}" data-method="delete-confirm"  data-type="danger"
                                   data-header-text="Вы действительно хотите удалить данного абонента навсегда?"
                                   data-confirm-text="Подтвердите свой пароль для продолжения">
                                    <i class="fa fa-trash-o"></i> Удаление абонента навсегда
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="panel">
                        <div class="panel-heading">
                            Другие счетчики
                        </div>

                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover report">
                                    <thead>
                                    <tr>
                                        <th>Тип</th>
                                        <th>Серийный номер</th>
                                        <th>Последнее показание</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(sizeof($other_meters) > 0)
                                        @foreach($other_meters as $meter)
                                            <tr>
                                                <td>{{$meter->type->name}}</td>
                                                <td>{{$meter->serial}}</td>
                                                <td>{{$meter->value}}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3">
                                                <p class="text-center mt-100 mb-100">Не найдено ни одного счетчика</p>
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection