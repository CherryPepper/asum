@extends('layouts.user', [
        'title' => 'Регистрация контрольного счетчика',
        'breadcrumbs' => [
            ['Регистрация контрольного счетчика']
        ]
    ])

@section('content')
    <div class="content add-control">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    <div class="panel">
                        <div class="panel-heading">
                            Данные счетчика
                        </div>
                        <div class="panel-body">

                            <div class="col-xs-12">
                                <form class="row meter-registration" action="{{route('meters.add.control.post')}}" method="post">
                                    <div class="row">
                                        <div class="form-wrapper col-sm-6">
                                            @if(!$errors->has('address.region'))
                                                <label>Район</label>
                                            @else
                                                <label class="text-danger">{{$errors->first('address.region')}}</label>
                                            @endif

                                            <div class="form-group {{$errors->has('address.region') ? 'has-error' : ''}}">
                                                <select class="form-control input-lg address-search" name="address[region]">
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

                                        <div class="form-wrapper col-sm-6">
                                            @if(!$errors->has('address.street'))
                                                <label>Улица</label>
                                            @else
                                                <label class="text-danger">{{$errors->first('address.street')}}</label>
                                            @endif

                                            <div class="form-group {{$errors->has('address.street') ? 'has-error' : ''}}">
                                                <select class="form-control input-lg address-search" name="address[street]" {{old('address.street') ? '' : 'disabled'}}>
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

                                        <div class="form-wrapper col-sm-3">
                                            @if(!$errors->has('address.home'))
                                                <label>Дом</label>
                                            @else
                                                <label class="text-danger">{{$errors->first('address.home')}}</label>
                                            @endif

                                            <div class="form-group {{$errors->has('address.home') ? 'has-error' : ''}}">
                                                <select class="form-control input-lg address-search" name="address[home]" {{old('address.home') ? '' : 'disabled'}}>
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
                                        <div class="form-wrapper col-sm-3">
                                            @if(!$errors->has('address.apartment'))
                                                <label>Квартира</label>
                                            @else
                                                <label class="text-danger">{{$errors->first('address.apartment')}}</label>
                                            @endif

                                            <div class="form-group {{$errors->has('address.apartment') ? 'has-error' : ''}}">
                                                <select class="form-control input-lg address-search" name="address[apartment]" {{old('address.apartment') ? '' : 'disabled'}}>
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
                                            @if(!$errors->has('rate_id'))
                                                <label>Тариф</label>
                                            @else
                                                <label class="text-danger">{{$errors->first('rate_id')}}</label>
                                            @endif

                                            <div class="form-group {{$errors->has('rate_id') ? 'has-error' : ''}}">
                                                <select class="form-control input-lg custom-select" name="rate_id">
                                                    <option></option>
                                                    @foreach($rates as $rate)
                                                        <option value="{{$rate->id}}" {{old('rate_id') == $rate->id ? 'selected' : ''}}>
                                                            {{$rate->title}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

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

                                    <div class="row">
                                        <div class="form-wrapper col-sm-6" id="selectChilds">
                                            <label>Указать дочерние счетчики</label>

                                            <div class="form-group">
                                                <div class="input-group">
                                                    <input type="text" class="form-control input-lg" readonly>

                                                    <a href="{{route('meter.select.childs')}}" title="Выбрать дочерние счетчики"
                                                       class="hidden"></a>
                                                    <input type="hidden" name="childs" id="childsList">

                                                    <span class="input-group-addon">
                                                        <span class="fa fa-sitemap"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-wrapper col-sm-12">
                                            @if(!$errors->has('description'))
                                                <label>Описание <small>(Будет подставлено в отчеты)</small></label>
                                            @else
                                                <label class="text-danger">{{$errors->first('description')}}</label>
                                            @endif

                                            <textarea class="form-control" name="description" style="height: 150px">{{old('description')}}</textarea>
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