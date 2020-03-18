@extends('layouts.user', [
        'title' => 'Редактирование абонента: '.$user->first_name.' '.$user->last_name,
        'breadcrumbs' => [
            ['Поиск абонента', route('client.list')],
            [$user->first_name.' '.$user->last_name, route('client.view', ['id' => $user->id])],
            ['Редактирование']
        ]
    ])

@section('content')
    <div class="content edit-client">
        <div class="container-fluid">
            <div class="row">
                <div class="panel">
                    <div class="panel-heading">
                        Редактирование данных абонента
                    </div>
                    <div class="panel-body">
                        <form class="row" action="{{route('client.edit.post')}}" method="post">
                            {{ csrf_field() }}
                            <input type="hidden" name="id" value="{{$user->id}}">

                            <div class="col-xs-12">
                                <div class="row">
                                    <div class="form-wrapper col-sm-4">
                                        @if(!$errors->has('first_name'))
                                            <label>Имя</label>
                                        @else
                                            <label class="text-danger">{{$errors->first('first_name')}}</label>
                                        @endif

                                        <div class="form-group {{$errors->has('first_name') ? 'has-error' : ''}}">
                                            <input type="text" class="form-control input-lg" name="first_name" value="{{old('first_name', $user->first_name)}}">
                                        </div>
                                    </div>
                                    <div class="form-wrapper col-sm-4">
                                        @if(!$errors->has('last_name'))
                                            <label>Фамилия</label>
                                        @else
                                            <label class="text-danger">{{$errors->first('last_name')}}</label>
                                        @endif

                                        <div class="form-group {{$errors->has('last_name') ? 'has-error' : ''}}">
                                            <input type="text" class="form-control input-lg" name="last_name" value="{{old('last_name', $user->last_name)}}">
                                        </div>
                                    </div>
                                    <div class="form-wrapper col-sm-4">
                                        @if(!$errors->has('email'))
                                            <label>E-mail</label>
                                        @else
                                            <label class="text-danger">{{$errors->first('email')}}</label>
                                        @endif

                                        <div class="form-group {{$errors->has('email') ? 'has-error' : ''}}">
                                            <input type="email" class="form-control input-lg" name="email" value="{{old('email', $user->email)}}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-wrapper col-sm-4">
                                        <label>Документ удостоверяющий личность</label>
                                        <div class="form-group">
                                            <input type="text" class="form-control input-lg" value="Паспорт" readonly>
                                        </div>
                                    </div>
                                    <div class="form-wrapper col-sm-4">
                                        @if(!$errors->has('passport'))
                                            <label>Серия №</label>
                                        @else
                                            <label class="text-danger">{{$errors->first('passport')}}</label>
                                        @endif

                                        <div class="form-group {{$errors->has('passport') ? 'has-error' : ''}}">
                                            <input type="text" class="form-control input-lg" name="passport" value="{{old('passport', $user->passport)}}">
                                        </div>
                                    </div>
                                    <div class="form-wrapper col-sm-4">
                                        @if(!$errors->has('passport_mvd'))
                                            <label>Кем и когда выдан</label>
                                        @else
                                            <label class="text-danger">{{$errors->first('passport_mvd')}}</label>
                                        @endif

                                        <div class="form-group {{$errors->has('passport_mvd') ? 'has-error' : ''}}">
                                            <input type="text" class="form-control input-lg" name="passport_mvd" value="{{old('passport_mvd', $user->passport_mvd)}}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-wrapper col-sm-4">
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
                                                        <option value="{{$region->id}}" {{old('address.region', @$address->region_id) == $region->id ? 'selected' : ''}}>
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
                                            <select class="form-control input-lg address-search" name="address[street]" {{old('address.street', @$address->street_id) ? '' : 'disabled'}}>
                                                @if((int)old('address.street', @$address->street_id))
                                                    <option></option>
                                                    @foreach($addresses['streets'] as $region)
                                                        <option value="{{$region->id}}" {{old('address.street', @$address->street_id) == $region->id ? 'selected' : ''}}>
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
                                            <select class="form-control input-lg address-search" name="address[home]" {{old('address.home', @$address->home_id) ? '' : 'disabled'}}>
                                                @if((int)old('address.home', @$address->home_id))
                                                    <option></option>
                                                    @foreach($addresses['homes'] as $region)
                                                        <option value="{{$region->id}}" {{old('address.home', @$address->home_id) == $region->id ? 'selected' : ''}}>
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
                                            <select class="form-control input-lg address-search" name="address[apartment]" {{old('address.apartment', @$address->apartment_id) ? '' : 'disabled'}}>
                                                @if((int)old('address.apartment', @$address->apartment_id))
                                                    <option></option>
                                                    @foreach($addresses['apartments'] as $region)
                                                        <option value="{{$region->id}}" {{old('address.apartment', @$address->apartment_id) == $region->id ? 'selected' : ''}}>
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
                                        @if(!$errors->has('rate_id'))
                                            <label>Тариф</label>
                                        @else
                                            <label class="text-danger">{{$errors->first('rate_id')}}</label>
                                        @endif

                                        <div class="form-group {{$errors->has('rate_id') ? 'has-error' : ''}}">
                                            <select class="form-control input-lg custom-select" name="rate_id">
                                                <option></option>
                                                @foreach($rates as $rate)
                                                    <option value="{{$rate->id}}" {{old('rate_id', $user->meter->rate_id) == $rate->id ? 'selected' : ''}}>
                                                        {{$rate->title}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-wrapper col-sm-4">
                                        @if(!$errors->has('contract'))
                                            <label>Договор №</label>
                                        @else
                                            <label class="text-danger">{{$errors->first('contract')}}</label>
                                        @endif

                                        <div class="form-group {{$errors->has('contract') ? 'has-error' : ''}}">
                                            <input type="text" class="form-control input-lg" name="contract" value="{{old('contract', $user->contract)}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-wrapper col-sm-4">
                                        @if(!$errors->has('login'))
                                            <label>Логин</label>
                                        @else
                                            <label class="text-danger">{{$errors->first('login')}}</label>
                                        @endif

                                        <div class="form-group {{$errors->has('login') ? 'has-error' : ''}}">
                                            <input type="text" class="form-control input-lg" name="login" value="{{old('login', $user->login)}}">
                                        </div>
                                    </div>
                                    <div class="form-wrapper col-sm-4">
                                        @if(!$errors->has('password'))
                                            <label>Пароль</label>
                                        @else
                                            <label class="text-danger">{{$errors->first('password')}}</label>
                                        @endif

                                        <div class="form-group {{$errors->has('password') ? 'has-error' : ''}}">
                                            <input type="password" class="form-control input-lg" name="password">
                                        </div>
                                    </div>
                                    <div class="form-wrapper col-sm-4">
                                        @if(!$errors->has('password_confirmation'))
                                            <label>Подтвердите пароль</label>
                                        @else
                                            <label class="text-danger">{{$errors->first('password_confirmation')}}</label>
                                        @endif

                                        <div class="form-group {{$errors->has('password_confirmation') ? 'has-error' : ''}}">
                                            <input type="password" class="form-control input-lg" name="password_confirmation">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="clear mb-30"></div>

                            <div class="other-meters col-xs-12">
                                <p class="title">Дополнительные счетчики</p>

                                @foreach($other_meters as $meter)
                                    <div class="col-sm-3 meter mb-20">
                                        <label class="mb-15">
                                        <span class="name">
                                            <input type="checkbox" class="hidden" name="other_meters[{{$meter->id}}]"
                                                   @if(!empty(old('other_meters.'.$meter->id, @$o_user_meters[$meter->id]))) checked @endif>
                                            <span class="checkbox-placeholder"></span>
                                            {{$meter->name}}
                                        </span>
                                        </label>

                                        <div class="meter-fields" @if(!empty(old('other_meters.'.$meter->id, @$o_user_meters[$meter->id]))) style="opacity: 1" @endif>
                                            <div class="form-wrapper">
                                                @if(!$errors->has('other_meters.'.$meter->id.'.serial'))
                                                    <label>Серийный номер</label>
                                                @else
                                                    <label class="text-danger">{{$errors->first('other_meters.'.$meter->id.'.serial')}}</label>
                                                @endif

                                                <div class="form-group {{$errors->has('other_meters.'.$meter->id.'.serial') ? 'has-error' : ''}}">
                                                    <input type="text" class="form-control input-lg" name="other_meters[{{$meter->id}}][serial]" value="{{old('other_meters.'.$meter->id.'.serial', @$o_user_meters[$meter->id]->serial)}}"
                                                           @if(empty(old('other_meters.'.$meter->id, @$o_user_meters[$meter->id]))) disabled @endif>
                                                </div>
                                            </div>

                                            <div class="form-wrapper">
                                                @if(!$errors->has('other_meters.'.$meter->id.'.value'))
                                                    <label>Показание</label>
                                                @else
                                                    <label class="text-danger">{{$errors->first('other_meters.'.$meter->id.'.value')}}</label>
                                                @endif

                                                <div class="form-group {{$errors->has('other_meters.'.$meter->id.'.value') ? 'has-error' : ''}}">
                                                    <input type="text" class="form-control input-lg" name="other_meters[{{$meter->id}}][value]" value="{{old('other_meters.'.$meter->id.'.value', @$o_user_meters[$meter->id]->value)}}"
                                                           @if(isset($o_user_meters[$meter->id]->value)) readonly @endif
                                                           @if(empty(old('other_meters.'.$meter->id, @$o_user_meters[$meter->id]))) disabled @endif>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="col-sm-12 mb-30">
                                <button type="submit" class="btn btn-info send-form">Сохранить</button>
                                <a href="{{route('client.view', ['id' => $user->id])}}" type="submit" class="btn btn-default send-form">Отмена</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection