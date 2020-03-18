@extends('layouts.user', [
        'title' => 'Редактирование сотрудника - '.$employer->first_name.' '.$employer->last_name,
        'breadcrumbs' => [
            ['Список сотрудников', route('employer.list')],
            ['Редактирование сотрудника - '.$employer->first_name.' '.$employer->last_name]
        ]
    ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <div class="panel">
                        <div class="panel-heading">
                            Редактирование данных сотрудника
                        </div>
                        <div class="panel-body">
                            <form class="row" action="{{route('employer.edit.post')}}" method="post">
                                {{ csrf_field() }}
                                <input type="hidden" name="id" value="{{$employer->id}}">

                                <div class="col-xs-12">
                                    <div class="row">
                                        <div class="form-wrapper col-sm-6">
                                            @if(!$errors->has('first_name'))
                                                <label>Имя</label>
                                            @else
                                                <label class="text-danger">{{$errors->first('first_name')}}</label>
                                            @endif

                                            <div class="form-group {{$errors->has('first_name') ? 'has-error' : ''}}">
                                                <input type="text" class="form-control input-lg" name="first_name" value="{{old('first_name', $employer->first_name)}}">
                                            </div>
                                        </div>

                                        <div class="form-wrapper col-sm-6">
                                            @if(!$errors->has('last_name'))
                                                <label>Фамилия</label>
                                            @else
                                                <label class="text-danger">{{$errors->first('last_name')}}</label>
                                            @endif

                                            <div class="form-group {{$errors->has('last_name') ? 'has-error' : ''}}">
                                                <input type="text" class="form-control input-lg" name="last_name" value="{{old('last_name', $employer->last_name)}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-wrapper col-sm-6">
                                            @if(!$errors->has('email'))
                                                <label>E-mail</label>
                                            @else
                                                <label class="text-danger">{{$errors->first('email')}}</label>
                                            @endif

                                            <div class="form-group {{$errors->has('email') ? 'has-error' : ''}}">
                                                <input type="text" class="form-control input-lg" name="email" value="{{old('email', $employer->email)}}">
                                            </div>
                                        </div>

                                        <div class="form-wrapper col-sm-6">
                                            @if(!$errors->has('login'))
                                                <label>Логин</label>
                                            @else
                                                <label class="text-danger">{{$errors->first('login')}}</label>
                                            @endif

                                            <div class="form-group {{$errors->has('login') ? 'has-error' : ''}}">
                                                <input type="text" class="form-control input-lg" name="login" value="{{old('login', $employer->login)}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-wrapper col-sm-6">
                                            @if(!$errors->has('password'))
                                                <label>Пароль</label>
                                            @else
                                                <label class="text-danger">{{$errors->first('password')}}</label>
                                            @endif

                                            <div class="form-group {{$errors->has('password') ? 'has-error' : ''}}">
                                                <input type="password" class="form-control input-lg" name="password" value="{{old('password')}}">
                                            </div>
                                        </div>

                                        <div class="form-wrapper col-sm-6">
                                            @if(!$errors->has('password_confirmation'))
                                                <label>Подтвердите пароль</label>
                                            @else
                                                <label class="text-danger">{{$errors->first('password_confirmation')}}</label>
                                            @endif

                                            <div class="form-group {{$errors->has('password_confirmation') ? 'has-error' : ''}}">
                                                <input type="password" class="form-control input-lg" name="password_confirmation" value="{{old('password_confirmation')}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-wrapper col-sm-6">
                                            @if(!$errors->has('role_id'))
                                                <label>Должность</label>
                                            @else
                                                <label class="text-danger">{{$errors->first('role_id')}}</label>
                                            @endif

                                            <div class="form-group {{$errors->has('role_id') ? 'has-error' : ''}}">
                                                <select class="form-control input-lg custom-select" name="role_id">
                                                    <option></option>
                                                    @foreach($roles as $role)
                                                        <option value="{{$role->id}}" {{old('role_id', $employer->role_id) == $role->id ? 'selected' : ''}}>
                                                            {{$role->name}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-sm-12">
                                    <input type="hidden" name="access_regions" id="selectedRegions" value="{{old('access_regions', $access_regions)}}">
                                    <button type="submit" class="btn btn-info send-form">Сохранить</button>
                                    <a href="{{route('employer.list')}}" class="btn btn-default">Отмена</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 added-addresses">
                    <div class="panel">
                        <div class="panel-heading">
                            Назначение районов
                        </div>
                        <div class="panel-body">
                            <div class="col-sm-4 regions">
                                <p class="title">Район</p>
                                <div class="list">
                                    <ul>
                                        <?php $access_regions = explode(',', old('access_regions', $access_regions));?>
                                        @foreach($addresses['regions'] as $region)
                                            <li>
                                                <label class="checkbox f-l">
                                                    <input type="checkbox" class="default" data-id="{{$region->id}}" @if(in_array($region->id, $access_regions)) checked @endif>
                                                    <span class="checkbox-placeholder"></span>
                                                </label>
                                                <a href="#" data-id="{{$region->id}}">
                                                    <span>{{$region->name}}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div class="col-sm-4 streets">
                                <p class="title">Улица</p>
                                <div class="list">
                                    <ul></ul>
                                </div>
                            </div>
                            <div class="col-sm-4 homes">
                                <p class="title">Дом</p>
                                <div class="list">
                                    <ul></ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection