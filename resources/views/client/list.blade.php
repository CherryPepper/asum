@extends('layouts.user', [
        'title' => 'Поиск абонента',
        'breadcrumbs' => [
            ['Поиск абонента'],
        ]
    ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel">
                        <div class="panel-heading">
                            Параметры поиска
                        </div>
                        <div class="panel-body">
                            <form action="{{route('client.list')}}">
                                <div class="row">
                                    <div class="form-wrapper col-sm-4">
                                        @if(!$errors->has('address.region'))
                                            <label>Район</label>
                                        @else
                                            <label class="text-danger">{{$errors->first('address.region')}}</label>
                                        @endif

                                        <div class="form-group {{$errors->has('address.region') ? 'has-error' : ''}}">
                                            <select class="form-control input-lg address-search" name="address[region]" data-create="0">
                                                <option></option>
                                                @foreach($addresses['regions'] as $region)
                                                    <option value="{{$region->id}}" {{app('request')->input('address.region') == $region->id ? 'selected' : ''}}>
                                                        {{$region->name}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-wrapper col-sm-4">
                                        <label>Улица</label>

                                        <div class="form-group">
                                            <select class="form-control input-lg address-search" name="address[street]" {{app('request')->input('address.street') ? '' : 'disabled'}} data-create="0">
                                                @if((int)app('request')->input('address.street'))
                                                    <option></option>
                                                    @foreach($addresses['streets'] as $region)
                                                        <option value="{{$region->id}}" {{app('request')->input('address.street') == $region->id ? 'selected' : ''}}>
                                                            {{$region->name}}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-wrapper col-sm-2">
                                        <label>Дом</label>

                                        <div class="form-group">
                                            <select class="form-control input-lg address-search" name="address[home]" {{app('request')->input('address.home') ? '' : 'disabled'}} data-create="0">
                                                @if((int)app('request')->input('address.home'))
                                                    <option></option>
                                                    @foreach($addresses['homes'] as $region)
                                                        <option value="{{$region->id}}" {{app('request')->input('address.home') == $region->id ? 'selected' : ''}}>
                                                            {{$region->name}}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-wrapper col-sm-2">
                                        <label>Квартира</label>

                                        <div class="form-group">
                                            <select class="form-control input-lg address-search" name="address[apartment]" {{app('request')->input('address.apartment') ? '' : 'disabled'}} data-create="0">
                                                @if((int)app('request')->input('address.apartment'))
                                                    <option></option>
                                                    @foreach($addresses['apartments'] as $region)
                                                        <option value="{{$region->id}}" {{app('request')->input('address.apartment') == $region->id ? 'selected' : ''}}>
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
                                        <label>Имя</label>

                                        <div class="form-group">
                                            <input type="text" class="form-control input-lg" name="name" value="{{app('request')->input('name')}}">
                                        </div>
                                    </div>

                                    <div class="form-wrapper col-sm-4">
                                        <label>Договор</label>

                                        <div class="form-group">
                                            <input type="text" class="form-control input-lg" name="contract" value="{{app('request')->input('contract')}}">
                                        </div>
                                    </div>

                                    <div class="form-wrapper col-sm-2">
                                        <label></label>
                                        <button type="submit" class="btn btn-info btn-block input-lg send-form mt-4">Применить</button>
                                    </div>
                                    <div class="form-wrapper col-sm-2">
                                        <label></label>
                                        <a type="submit" class="btn btn-default btn-block input-lg send-form mt-4"
                                           href="{{route('client.list')}}">Очистить</a>
                                    </div>
                                </div>
                            </form>

                            <div class="v-spacing-xs"></div>

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Имя</th>
                                        <th>Адрес</th>
                                        <th>Номер договора</th>
                                        <th>Тариф</th>
                                        <th>Дата добавления</th>
                                        <th>Действия</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($users->total()))
                                        @foreach($users as $user)
                                            <tr @if($user->display == 0) class="toggle-block danger" title="Пользователь удален"
                                                @elseif($user->meter->status_id == 3) class="toggle-block warning" title="У этого пользователя нет добавленного счетчика"  @endif>
                                                <td>{{$user->first_name .' '.$user->last_name}}</td>
                                                <td>{{\App\Helpers\Addresses::AdrString($user->meter->address)}}</td>
                                                <td>№ {{$user->contract}}</td>
                                                <td>{{$user->meter->rate->title}}</td>
                                                <td>
                                                    {{\Carbon\Carbon::parse($user->created_at)->format('d.m.Y')}}
                                                </td>

                                                <td class="form-actions-icons">
                                                    <div class="actions">
                                                        <a href="{{route('client.view', ['id' => $user->id])}}" data-toggle="tooltip"
                                                           title="Просмотр профиля" class="mr-10">
                                                            <span class="fa fa-address-card-o" aria-hidden="true"></span>
                                                        </a>

                                                        @if($user->display !== 0)
                                                            <a href="#" class="text-danger toggle-block"
                                                               data-toggle="modal" data-target="#modalConfirmation"
                                                               title="Удалить пользователя" data-url="{{route('client.delete.post')}}"
                                                                data-id="{{$user->id}}" data-method="delete-confirm"  data-type="danger"
                                                                data-header-text="Вы действительно хотите удалить данного абонента?"
                                                                data-confirm-text="Подтвердите свой пароль для продолжения">
                                                                <span class="fa fa-trash-o" aria-hidden="true"></span>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6">
                                                <p class="text-center mt-100 mb-100">Не найдено ни одного абонента</p>
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>

                            <div class="pull-right">
                                <?php $get = $_GET; unset($get['page'])?>
                                {{$users->appends($get)->links()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection