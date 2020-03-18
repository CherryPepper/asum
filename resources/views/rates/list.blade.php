@extends('layouts.user', [
        'title' => 'Список тарифов',
        'breadcrumbs' => [
            ['Список тарифов'],
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
                            <form action="{{route('rate.list')}}">
                                <div class="row">
                                    <div class="form-wrapper col-sm-4">
                                        <label>Тип</label>
                                        <div class="form-group">
                                            <select class="form-control input-lg custom-select" name="type">
                                                <option>Все</option>
                                                <option value="1" @if(app('request')->input('type') == 1) selected @endif>Обычный</option>
                                                <option value="2" @if(app('request')->input('type') == 2) selected @endif>Мультитарифный</option>
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
                                           href="{{route('rate.list')}}">Очистить</a>
                                    </div>
                                </div>
                            </form>

                            <div class="v-spacing-xs"></div>

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Название</th>
                                        <th>Описание</th>
                                        <th>Тип</th>
                                        <th>Интервалы</th>
                                        <th>Дата добавления</th>
                                        <th>Действия</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($rates->total()))
                                        @foreach($rates as $rate)
                                            <tr>
                                                <td>{{$rate->title}}</td>
                                                <td>{{$rate->description}}</td>
                                                <td>{{$rate->type == 1 ? 'Обычный' : 'Мультитарифный'}}</td>
                                                <td>
                                                    @foreach($rate->intervals as $interval)
                                                        <?php
                                                            $time_start = $interval->time_start/60;
                                                            $time_end = $interval->time_end/60;

                                                            $time_start = ($time_start < 10) ? '0'.$time_start.':00' : $time_start.':00';
                                                            $time_end = ($time_end < 10) ? '0'.$time_end.':00' : $time_end.':00';
                                                        ?>
                                                        <span><strong>{{$time_start}} - {{$time_end}}:</strong> {{$interval->price}} руб</span>
                                                        <br>
                                                    @endforeach
                                                </td>
                                                <td>{{\Carbon\Carbon::parse($rate->created_at)->format('d.m.Y')}}</td>
                                                <td  class="form-actions-icons">
                                                    <div class="actions">
                                                        <a href="#" class="text-danger toggle-block"
                                                           data-toggle="modal" data-target="#modalConfirmation"
                                                           title="Удалить тариф" data-url="{{route('rate.delete.post')}}"
                                                           data-id="{{$rate->id}}" data-method="delete-confirm"  data-type="danger"
                                                           data-header-text="Вы действительно хотите удалить данный тариф?"
                                                           data-confirm-text="Подтвердите свой пароль для продолжения">
                                                            <span class="fa fa-trash-o" aria-hidden="true"></span>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6">
                                                <p class="text-center mt-100 mb-100">Не найдено ни одного тарифа</p>
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>

                            <div class="pull-right">
                                <?php $get = $_GET; unset($get['page'])?>
                                {{$rates->appends($get)->links()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection