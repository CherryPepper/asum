@extends('layouts.user', [
        'title' => 'Регистрация счетчика',
        'breadcrumbs' => [
            ['Регистрация счетчика', route('meters.registration')],
            [$meter->user->first_name.' '.$meter->user->last_name],
        ]
    ])

@section('content')
    <div class="content meters-registration">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    <div class="panel">
                        <div class="panel-heading">
                            Данные пользователя
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive user-info">
                                <table class="table table-sm">
                                    <tbody>
                                    <tr>
                                        <td class="bg-gray">Договор</td>
                                        <td>{{$meter->user->contract}}</td>

                                        <td class="bg-gray">Статус счетчика</td>
                                        <td>{{$meter->status->name}}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray">Имя</td>
                                        <td>{{$meter->user->first_name .' '. $meter->user->last_name}}</td>

                                        <td class="bg-gray">Тариф</td>
                                        <td>{{$meter->rate->title}}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray">E-mail</td>
                                        <td>{{$meter->user->email}}</td>

                                        <td class="bg-gray">Текущее показание</td>
                                        <td>0 Квт</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray">Адрес</td>
                                        <td>{{\App\Helpers\Addresses::AdrString($meter->address)}}</td>

                                        <td class="bg-gray">Баланс</td>
                                        <td>0 руб</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray">Паспорт</td>
                                        <td>{{$meter->user->passport}}</td>

                                        <td class="bg-gray">Дата добавления</td>
                                        <td>{{\Carbon\Carbon::parse($meter->created_at)->format('d.m.Y')}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-xs-12 mt-50">
                                <form class="row meter-registration" action="{{route('meters.registration.post')}}" method="post">
                                    <input type="hidden" name="nst_lvl" value="0">
                                    <input type="hidden" name="id" value="{{$meter->id}}">

                                    <div class="row">
                                        <div class="form-wrapper col-sm-6">
                                            @if(!$errors->has('ip_address'))
                                                <label>IP адрес</label>
                                            @else
                                                <label class="text-danger">{{$errors->first('ip_address')}}</label>
                                            @endif

                                            <div class="form-group {{$errors->has('ip_address') ? 'has-error' : ''}}">
                                                <input type="text" class="form-control input-lg" name="ip_address" value="{{old('ip_address')}}">
                                            </div>
                                        </div>
                                        <div class="form-wrapper col-sm-6">
                                            @if(!$errors->has('serial'))
                                                <label>Серийный номер</label>
                                            @else
                                                <label class="text-danger">{{$errors->first('serial')}}</label>
                                            @endif

                                            <div class="form-group {{$errors->has('serial') ? 'has-error' : ''}}">
                                                <input type="text" class="form-control input-lg" name="serial" value="{{old('serial')}}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-wrapper col-sm-6">
                                            @if(!$errors->has('operator_id'))
                                                <label>Оператор</label>
                                            @else
                                                <label class="text-danger">{{$errors->first('operator_id')}}</label>
                                            @endif

                                            <div class="form-group {{$errors->has('operator_id') ? 'has-error' : ''}}">
                                                <select class="form-control input-lg custom-select" name="operator_id">
                                                    <option></option>
                                                    @foreach($operators as $operator)
                                                        <option value="{{$operator->id}}" {{old('operator_id') == $operator->id ? 'selected' : ''}}>
                                                            {{$operator->name}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-12 mt-20">
                                        <div class="row">
                                            <div class="mb-20 meter-registration-progress">
                                                <h5> Прогресс регистрации счетчика </h5>
                                                <div class="progress progress-striped active">
                                                    <div style="width: 5%" class="progress-bar progress-bar-info">
                                                        <span>5%</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <button type="submit" class="btn btn-info send-ajax-form">Начать регистрацию </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection