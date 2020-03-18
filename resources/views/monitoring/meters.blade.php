@php
    $get = $_GET; unset($get['page']);
    $cur_type = app('request')->input('type', 0);
@endphp

@extends('layouts.user', [
        'title' => $types[$cur_type]['title'],
        'breadcrumbs' => [
            ['Мониторинг счетчиков'],
            [$types[$cur_type]['title']],
        ]
    ])

@section('content')
    <div class="content monitoring missed-points">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel">
                        <div class="panel-heading">
                            Мониторинг счетчиков
                        </div>
                        <div class="panel-body">
                            <div class="mb-30">
                                <form action="{{route('monitoring.meters')}}">
                                    <input type="hidden" name="type" value="{{$cur_type}}">

                                    <div class="col-sm-6">
                                        <div class="form-wrapper col-sm-6">
                                            <label>Район</label>

                                            <div class="form-group">
                                                <select class="form-control input-lg address-search" name="address[region]">
                                                    @if(!(int)@$get['address']['region'] && (@$get['address']['region'] !== null))
                                                        <option value="{{@$get['address']['region']}}">{{@$get['address']['region']}}</option>
                                                    @else
                                                        <option></option>
                                                        @foreach($addresses['regions'] as $region)
                                                            <option value="{{$region->id}}" {{@$get['address']['region'] == $region->id ? 'selected' : ''}}>
                                                                {{$region->name}}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-wrapper col-sm-6">
                                            <label>Улица</label>

                                            <div class="form-group">
                                                <select class="form-control input-lg address-search" name="address[street]" {{@$get['address']['street'] ? '' : 'disabled'}}>
                                                    @if((int)@$get['address']['street'])
                                                        <option></option>
                                                        @foreach($addresses['streets'] as $region)
                                                            <option value="{{$region->id}}" {{@$get['address']['street'] == $region->id ? 'selected' : ''}}>
                                                                {{$region->name}}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-wrapper col-sm-3">
                                            <label>Дом</label>

                                            <div class="form-group">
                                                <select class="form-control input-lg address-search" name="address[home]" {{@$get['address']['home'] ? '' : 'disabled'}}>
                                                    @if((int)@$get['address']['home'])
                                                        <option></option>
                                                        @foreach($addresses['homes'] as $region)
                                                            <option value="{{$region->id}}" {{@$get['address']['home'] == $region->id ? 'selected' : ''}}>
                                                                {{$region->name}}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-wrapper col-sm-3">
                                            <label>Квартира</label>

                                            <div class="form-group">
                                                <select class="form-control input-lg address-search" name="address[apartment]" {{@$get['address']['apartment'] ? '' : 'disabled'}}>
                                                    @if((int)@$get['address']['apartment'])
                                                        <option></option>
                                                        @foreach($addresses['apartments'] as $region)
                                                            <option value="{{$region->id}}" {{@$get['address']['apartment'] == $region->id ? 'selected' : ''}}>
                                                                {{$region->name}}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-wrapper col-sm-3">
                                            <label></label>
                                            <button type="submit" class="btn btn-info btn-block input-lg send-form mt-4">Применить</button>
                                        </div>

                                        <div class="form-wrapper col-sm-3">
                                            <label></label>
                                            <a type="submit" class="btn btn-default btn-block input-lg send-form mt-4" href="{{route('monitoring.meters', ['type' => $cur_type])}}">Очистить</a>
                                        </div>
                                    </div>

                                    <div class="clear"></div>
                                </form>
                            </div>

                            <ul class="nav nav-tabs">
                                @foreach($types as $key=>$type)
                                    <li role="presentation" @if($key == $cur_type) class="active" @endif>
                                        <a href="{{route('monitoring.meters', ['type' => $key])}}">
                                            {{$type['title']}}
                                            @if($key == $cur_type) <span class="badge">{{$meters->total()}}</span> @endif
                                        </a>
                                    </li>
                                @endforeach
                            </ul>

                            @include('monitoring.'.$types[$cur_type]['view'])

                            <div class="pull-right">
                                {{$meters->appends($get)->links()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection