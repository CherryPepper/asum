@extends('layouts.user', [
        'title' => 'Добавить задание',
        'breadcrumbs' => [
            ['Добавить задание'],
        ]
    ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="panel">
                    <div class="panel-heading">
                        Укажите данные задания
                    </div>
                    <div class="panel-body">
                        <form class="row" action="{{route('task.create.post')}}" method="post">
                            {{ csrf_field() }}

                            <div class="col-xs-12">
                                <div class="row">
                                    <div class="form-wrapper col-sm-4">
                                        @if(!$errors->has('address.region'))
                                            <label>Район</label>
                                        @else
                                            <label class="text-danger">{{$errors->first('address.region')}}</label>
                                        @endif

                                        <div class="form-group {{$errors->has('address.region') ? 'has-error' : ''}}">
                                            <select class="form-control input-lg address-search" name="address[region]" data-create="0">
                                                @if(!(int)old('address.region') && (old('address.region') !== null))
                                                    <option value="{{old('address.region')}}">{{old('address.region')}}</option>
                                                @else
                                                    <option></option>
                                                    @foreach($addresses['regions'] as $region)
                                                        <option value="{{$region->id}}" {{old('address.region') == $region->id ? 'selected' : ''}}>
                                                            {{$region->name}}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-wrapper col-sm-4">
                                        @if(!$errors->has('address.street'))
                                            <label>Улица</label>
                                        @else
                                            <label class="text-danger">{{$errors->first('address.street')}}</label>
                                        @endif

                                        <div class="form-group {{$errors->has('address.street') ? 'has-error' : ''}}">
                                            <select class="form-control input-lg address-search" name="address[street]" {{old('address.street') ? '' : 'disabled'}} data-create="0">
                                                @if((int)old('address.street'))
                                                    <option></option>
                                                    @foreach($addresses['streets'] as $region)
                                                        <option value="{{$region->id}}" {{old('address.street') == $region->id ? 'selected' : ''}}>
                                                            {{$region->name}}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-wrapper col-sm-2">
                                        @if(!$errors->has('address.home'))
                                            <label>Дом</label>
                                        @else
                                            <label class="text-danger">{{$errors->first('address.home')}}</label>
                                        @endif

                                        <div class="form-group {{$errors->has('address.home') ? 'has-error' : ''}}">
                                            <select class="form-control input-lg address-search" name="address[home]" {{old('address.home') ? '' : 'disabled'}} data-create="0">
                                                @if((int)old('address.home'))
                                                    <option></option>
                                                    @foreach($addresses['homes'] as $region)
                                                        <option value="{{$region->id}}" {{old('address.home') == $region->id ? 'selected' : ''}}>
                                                            {{$region->name}}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-wrapper col-sm-2">
                                        @if(!$errors->has('address.apartment'))
                                            <label>Квартира</label>
                                        @else
                                            <label class="text-danger">{{$errors->first('address.apartment')}}</label>
                                        @endif

                                        <div class="form-group {{$errors->has('address.apartment') ? 'has-error' : ''}}">
                                            <select class="form-control input-lg address-search" name="address[apartment]" {{old('address.apartment') ? '' : 'disabled'}} data-create="0">
                                                @if((int)old('address.apartment'))
                                                    <option></option>
                                                    @foreach($addresses['apartments'] as $region)
                                                        <option value="{{$region->id}}" {{old('address.apartment') == $region->id ? 'selected' : ''}}>
                                                            {{$region->name}}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-wrapper col-sm-4">
                                        @if(!$errors->has('role_id'))
                                            <label>Должность</label>
                                        @else
                                            <label class="text-danger">{{$errors->first('role_id')}}</label>
                                        @endif

                                        <div class="form-group {{$errors->has('role_id') ? 'has-error' : ''}}">
                                            <select class="form-control input-lg custom-select getEmployers" name="role_id">
                                                <option></option>
                                                @foreach($roles as $role)
                                                    <option value="{{$role->id}}" {{old('role_id') == $role->id ? 'selected' : ''}}>
                                                        {{$role->name}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-wrapper col-sm-4">
                                        @if(!$errors->has('employer_id'))
                                            <label>Сотрудник</label>
                                        @else
                                            <label class="text-danger">{{$errors->first('employer_id')}}</label>
                                        @endif

                                        <div class="form-group {{$errors->has('employer_id') ? 'has-error' : ''}}">
                                            <select class="form-control custom-select input-lg employersResult" name="employer_id">
                                                <option></option>
                                                @foreach($employers as $employer)
                                                    <option value="{{$employer->value}}" {{old('employer_id') == $employer->value ? 'selected' : ''}}>
                                                        {{$employer->text}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-wrapper col-sm-2">
                                        @if(!$errors->has('date_start'))
                                            <label>Дата начала</label>
                                        @else
                                            <label class="text-danger">{{$errors->first('date_start')}}</label>
                                        @endif

                                        <div class="form-group {{$errors->has('date_start') ? 'has-error' : ''}}">
                                            <div class="input-group date">
                                                <input type="text" class="form-control input-lg" id="date-from" name="date_start" value="{{old('date_start')}}">
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-wrapper col-sm-2">
                                        @if(!$errors->has('date_end'))
                                            <label>Дата конца</label>
                                        @else
                                            <label class="text-danger">{{$errors->first('date_end')}}</label>
                                        @endif

                                        <div class="form-group {{$errors->has('date_end') ? 'has-error' : ''}}">
                                            <div class="input-group date">
                                                <input type="text" class="form-control input-lg" id="date-to" name="date_end" value="{{old('date_end')}}">
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-wrapper col-sm-8">
                                        @if(!$errors->has('message'))
                                            <label>Описание задания</label>
                                        @else
                                            <label class="text-danger">{{$errors->first('message')}}</label>
                                        @endif

                                        <div class="form-group {{$errors->has('message') ? 'has-error' : ''}}">
                                            <textarea class="form-control" name="message" placeholder="Добавьте описание задания..." style="height: 150px;">{{old('message')}}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-info send-form">Сохранить</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection