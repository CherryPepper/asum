@extends('layouts.user', [
        'title' => 'Добавить тариф',
        'breadcrumbs' => [
            ['Добавить тариф'],
        ]
    ])

@section('content')
    <div class="content rate-create">
        <div class="container-fluid">
            <div class="row">
                <form class="row" action="{{route('rate.create.post')}}" method="post">
                    {{ csrf_field() }}

                    <div class="col-sm-6">
                        <div class="panel">
                            <div class="panel-heading">
                                Укажите данные тарифа
                            </div>
                            <div class="panel-body">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="form-wrapper col-sm-6">
                                            @if(!$errors->has('title'))
                                                <label>Название тарифа</label>
                                            @else
                                                <label class="text-danger">{{$errors->first('title')}}</label>
                                            @endif

                                            <div class="form-group {{$errors->has('title') ? 'has-error' : ''}}">
                                                <input type="text" class="form-control input-lg" name="title" value="{{old('title')}}">
                                            </div>
                                        </div>

                                        <div class="form-wrapper col-sm-6">
                                            <label>Тип тарифа</label>

                                            <ul class="list-unstyled list-inline mt-10">
                                                <li>
                                                    <label class="radio">
                                                        <input type="radio" name="type" value="1" checked>
                                                        <span></span>
                                                        Обычный
                                                    </label>
                                                </li>
                                                <li>
                                                    <label class="radio">
                                                        <input type="radio" name="type" value="2">
                                                        <span></span>
                                                        Мультитарифный
                                                    </label>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-wrapper col-sm-12">
                                            @if(!$errors->has('description'))
                                                <label>Описание тарифа</label>
                                            @else
                                                <label class="text-danger">{{$errors->first('description')}}</label>
                                            @endif

                                            <div class="form-group {{$errors->has('description') ? 'has-error' : ''}}">
                                                <textarea class="form-control" name="description" placeholder="Описание тарифа (не обязательно)" style="height: 150px;">{{old('description')}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-info send-form">Сохранить</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="panel" style="min-height: 409px">
                            <div class="panel-heading">
                                Временные интервалы
                            </div>
                            <div class="panel-body">
                                <div class="col-lg-6">
                                    <div class="buttons mb-10" style="display: none">
                                        <div class="row" id="firstPointBlock">
                                            <a class="btn btn-outline btn-block btn-info" id="appleFirstPoint">
                                                <i class="fa fa-check-circle-o"></i> Применить начальную точку
                                            </a>
                                        </div>
                                        <div class="row" style="display: none" id="pointsNavBlock">
                                            <a class="btn btn-outline btn-info col-sm-4" id="createNewPoint">
                                                <i class="fa fa-plus-circle"></i> Новая точка
                                            </a>
                                            <a class="btn btn-outline btn-default col-sm-4" id="clearIntervals">
                                                <i class="fa fa-times-circle-o"></i> Очистить
                                            </a>
                                            <a class="btn btn-outline btn-success col-sm-4" id="appleIntervals">
                                                <i class="fa fa-check-circle-o"></i> Применить
                                            </a>
                                        </div>
                                    </div>

                                    <div class="intervals center-block" style="width: 270px">
                                        <img src="/images/24-hours.png" class="clock-img">

                                        <div class="knobs">
                                            <input type="text" class="knob" data-min="0" data-max="24" value="24"
                                                  readonly data-thickness=".1" data-displayInput=false>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6 intervals-inputs mt-50">
                                    <div class="form-wrapper col-sm-12" style="background-color: #87ceeb">
                                        <div class="form-group col-lg-4">
                                            <label>От</label>
                                            <input type="text" class="form-control input-sm" name="time_start[]" value="00:00" readonly>
                                        </div>

                                        <div class="form-group col-lg-4">
                                            <label>До</label>
                                            <input type="text" class="form-control input-sm" name="time_end[]" value="24:00" readonly>
                                        </div>

                                        <div class="form-group col-lg-4">
                                            <label>Цена (1КВТ)</label>
                                            <input type="text" class="form-control input-sm has-error" name="price[]" value="0.00">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="template-knobs" class="hidden">
        <input type="text" data-min="0" data-max="24" value="24" readonly
               data-thickness=".1">
    </div>

    <div id="template-intervals-inputs" class="hidden">
        <div class="row">
            <div class="form-wrapper col-sm-12" style="background-color: #87ceeb">
                <div class="form-group col-lg-4">
                    <label>От</label>
                    <input type="text" class="form-control input-sm" name="time_start[]" value="00:00" readonly>
                </div>

                <div class="form-group col-lg-4">
                    <label>До</label>
                    <input type="text" class="form-control input-sm" name="time_end[]" value="24:00" readonly>
                </div>

                <div class="form-group col-lg-4">
                    <label>Цена (За 1Квт)</label>
                    <input type="text" class="form-control input-sm" name="price[]" value="0.00">
                </div>
            </div>
        </div>
    </div>
@endsection
