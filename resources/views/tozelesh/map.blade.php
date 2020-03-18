@extends('layouts.user', [
        'title' => 'Карта светильников',
        'breadcrumbs' => [
            ['Карта светильников']
        ]
    ])

@section('content')
    <div class="hidden">
        <div title="Добавить новый объект" id="map-new-object">
            <p><i class="fa fa-plus"></i> Добавить объект</p>
        </div>

        <div id="map-steps">
            <input type="hidden" value="0" id="current-step">
            <input type="hidden" id="object-id">
            <div id="step-1" class="active">
                <p class="step-num">Шаг 1</p>
                <p class="desc">Укажите на карте расположение счетчика</p>
            </div>
            <div id="step-2">
                <p class="step-num">Шаг 2</p>
                <p class="desc">Укажите на карте расположение светильников</p>
            </div>
            <div id="step-3">
                <p class="step-num">Шаг 3</p>
                <p class="desc">Задайте расписание вкл/выкл светильников</p>

                <div class="row mt-20 hidden" id="set-time">
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="form-wrapper col-sm-6">
                                <label>Время вкл</label>

                                <div class="form-group">
                                    <div class="input-group date">
                                        <input type="text" class="form-control input-sm" id="time-on">
                                        <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-wrapper col-sm-6">
                                <label>Время выкл</label>

                                <div class="form-group">
                                    <div class="input-group date">
                                        <input type="text" class="form-control input-sm" id="time-off">
                                        <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="clear"></div>
            </div>

            <button class="btn btn-danger btn-block input-lg mr-5" id="cancel">Отмена</button>
            <button class="btn btn-success btn-block input-lg hidden" id="save-object">Сохранить</button>
        </div>

        <div id="map-lamps">
            <div class="wrapper-lamps">
                @foreach($lamp_types as $lamp)
                    <div class="lamp" data-type="{{$lamp->id}}">
                        <img src="/images/tozelesh/black/{{$lamp->img}}">
                        <p>
                            <span class="title">{{$lamp->name}}</span>
                            <br>
                            <span class="consumption">Потребление: {{$lamp->consumption}} Квт</span>
                        </p>
                    </div>
                @endforeach
            </div>

            <div class="buttons">
                <button class="btn btn-danger btn-block input-lg mr-5" id="remove">Удалить выбранные</button>
                <button class="btn btn-success btn-block input-lg" id="save">Сохранить</button>
                <a class="btn btn-default btn-block hidden" href="/" id="cancel">Отменить редактирование</a>
            </div>

            <div class="hidden" id="tmp-lamps"></div>
        </div>

        <div id="objects">
            @foreach($objects as $object)
                <input type="hidden" class="meters" data-meter="{{$object->meter_id}}"  data-id="{{$object->id}}"
                       data-address="{{\App\Helpers\Addresses::AdrString($object->meter->address)}}"
                       data-status="{{$object->meter->status_id}}" data-coordinates="{{$object->coordinates}}"
                       data-time_on="{{$object->time_on}}" data-time_off="{{$object->time_off}}"
                       data-value="{{$object->meter->value}}">

                @foreach($object->lamps as $lamp)
                    <input type="hidden" class="lamps-{{$object->id}}" data-id="{{$lamp->id}}" data-type="{{$lamp->lamp_id}}"
                           data-consumption="{{$lamp->type->consumption}}" data-coordinates="{{$lamp->coordinates}}"
                            data-img="{{$lamp->type->img}}">
                @endforeach
            @endforeach
        </div>

        <div id="tooltip-info">
            <div class="table-responsive tooltip-info">
                <input type="hidden" id="object-id">
                <table class="table table-sm">
                    <tbody>
                    <tr id="change-meter-status">
                        <td class="bg-gray">Статус</td>
                        <td>
                            <span class="status"></span> &nbsp;
                            <span id="on-off">( <a href="#" id="set-meter-status"></a> )</span>
                        </td>
                    </tr>
                    <tr id="refresh-meter-value">
                        <td class="bg-gray">Последнее показание</td>
                        <td>
                            <span id="last-value"></span> Квт
                            &nbsp;
                            <a href="#" title="Обновить показание" id="refresh-btn">
                                <i class="fa fa-refresh"></i>
                            </a>
                            <div class="mt-5">
                                <a id="edit-meter" href="#" title="Редактирование счетчика">
                                    <i class="fa fa-edit"></i>
                                    Редактирование счетчика
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="bg-gray">Адрес</td>
                        <td><span id="address"></span></td>
                    </tr>
                    <tr>
                        <td class="bg-gray">Кол-во фонарей</td>
                        <td>
                            <span id="lamps-cnt"></span>
                            &nbsp;
                            <a id="edit-object" href="#">
                                <i class="fa fa-edit"></i>
                                Редактирование объекта
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="bg-gray">Потребление фонарей</td>
                        <td>
                            <span id="lamps-consumption"></span> Квт &nbsp;
                            <a id="consumption-report" href="#">
                                <i class="fa fa-bar-chart-o"></i>
                                Отчет потребления
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="bg-gray">Время вкл/выкл</td>
                        <td><span id="time-on-off">21:35 / 7:35</span></td>
                    </tr>
                    </tbody>
                </table>

                <a class="btn btn-block btn-danger" id="delete-object" data-toggle="modal" data-target="#modalConfirmation" data-url="/object/delete" data-id="" data-method="delete-confirm" data-type="danger" data-header-text="Вы действительно хотите удалить объект?" data-confirm-text="Подтвердите свой пароль для продолжения">
                    <i class="fa fa-trash-o"></i> Удалить объект
                </a>
            </div>
        </div>
    </div>

    <!-- Main content-->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel">
                        <div class="panel-heading">
                            Карта светильников
                        </div>
                        <div class="panel-body">
                            <div id="google-map"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End of Main content-->
@endsection