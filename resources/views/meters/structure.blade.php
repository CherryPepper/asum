@php
    $breadcrumbs[] = ['Структура счетчиков', route('meters.structure')];
    if(isset($parents))
        foreach ($parents as $parent)
            $breadcrumbs[] = [$parent->serial, route('meters.structure', ['id' => $parent->id])];
@endphp
@extends('layouts.user', [
        'title' => 'Структура счетчиков',
        'breadcrumbs' => $breadcrumbs
    ])

@section('content')
    <div class="content meters-structure">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel">
                        <div class="panel-heading">
                            Список счетчиков
                        </div>

                        <div class="search-block">
                            <form action="{{route('meters.structure')}}">
                                <div class="row">
                                    <div class="form-wrapper col-sm-2">
                                        <label>IP адрес</label>

                                        <div class="form-group">
                                            <input type="text" class="form-control input-lg" name="ip_address" value="{{app('request')->input('ip_address')}}">
                                        </div>
                                    </div>

                                    <div class="form-wrapper col-sm-2">
                                        <label>Серийный номер</label>

                                        <div class="form-group">
                                            <input type="text" class="form-control input-lg" name="serial" value="{{app('request')->input('serial')}}">
                                        </div>
                                    </div>

                                    <div class="form-wrapper col-sm-2">
                                        <div class="form-group">
                                            <label></label>
                                            <button type="submit" class="btn btn-info btn-block input-lg send-form mt-4">Применить</button>
                                        </div>
                                    </div>

                                    <div class="form-wrapper col-sm-2">
                                        <div class="form-group">
                                            <label></label>
                                            <a type="submit" class="btn btn-default btn-block input-lg mt-4" href="{{route('meters.structure')}}">Очистить</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @if(!empty($meters->total()))
                        <div class="panel-body">
                            <div class="meters-list row">

                                @foreach($meters as $meter)
                                    <div class="col-lg-1 col-md-3 col-xs-4 meter-info" data-serial="{{$meter->serial}}"
                                        data-address="{{\App\Helpers\Addresses::AdrString($meter->address)}}" data-type="{{$meter->type_id}}"
                                        data-id="{{$meter->id}}" data-status="{{$meter->status->name}}">

                                        @if($meter->type_id == 1)
                                            <img src="/images/meter-ico.png">
                                        @else
                                            <img src="/images/folder-ico.png">
                                        @endif

                                        <span class="serial">{{$meter->serial}}</span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="pull-right">
                                {{$meters->links()}}
                            </div>
                        </div>
                        @else
                            <p class="text-center mt-100 mb-100">По данным критериям не найдено ни одного счетчика</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection