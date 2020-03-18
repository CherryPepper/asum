@extends('layouts.user', [
        'title' => 'Добавление общих показаний',
        'breadcrumbs' => [
            ['Добавление общих показаний'],
        ]
    ])

@section('content')
    <div class="content add-total-value">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <div class="panel">
                        <div class="panel-heading">
                            Добавить показание на {{\Carbon\Carbon::parse($current_date)->format('d.m.Y')}}
                        </div>
                        <div class="panel-body">
                            <form action="{{route('ometers.total_value.post')}}" method="post">
                                {{csrf_field()}}

                                <div class="row">
                                    <div class="form-wrapper col-sm-6">
                                        @if(!$errors->has('type_id'))
                                            <label>Тип счетчика</label>
                                        @else
                                            <label class="text-danger">{{$errors->first('type_id')}}</label>
                                        @endif

                                        <div class="form-group {{$errors->has('type_id') ? 'has-error' : ''}}">
                                            @foreach($meters as $meter)
                                                <input type="hidden" id="price-{{$meter->id}}" value="{{$meter->price}}">
                                            @endforeach

                                            <select class="form-control input-lg custom-select" id="type-id" name="type_id">
                                                <option></option>
                                                @foreach($meters as $meter)
                                                    <option value="{{$meter->id}}" {{old('type_id') == $meter->id ? 'selected' : ''}}>
                                                        {{$meter->name}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-wrapper col-sm-6">
                                        @if(!$errors->has('difference'))
                                            <label>Общее потребление</label>
                                        @else
                                            <label class="text-danger">{{$errors->first('difference')}}</label>
                                        @endif

                                        <div class="form-group {{$errors->has('difference') ? 'has-error' : ''}}">
                                            <input type="text" class="form-control input-lg" name="difference" value="{{old('difference')}}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-wrapper col-sm-3">
                                        @if(!$errors->has('price'))
                                            <label>Цена руб/М<sup>3</sup></label>
                                        @else
                                            <label class="text-danger">{{$errors->first('price')}}</label>
                                        @endif

                                        <div class="form-group {{$errors->has('price') ? 'has-error' : ''}}">
                                            <input type="text" class="form-control input-lg" name="price" id="price" value="{{old('price')}}" readonly>
                                        </div>
                                    </div>

                                    <div class="form-wrapper col-sm-3">
                                        <label></label>
                                        <button class="btn btn-default btn-block input-lg change-price">Изменить</button>
                                    </div>
                                </div>

                                <div class="row mt-20">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-info input-lg send-form">Сохранить</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="panel">
                        <div class="panel-heading">
                            История показаний
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover report">
                                    <thead>
                                    <tr>
                                        <th>Тип</th>
                                        <th>Потребление</th>
                                        <th>Начислено руб</th>
                                        <th>Дата</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($values->total()))
                                        @foreach($values as $value)
                                            <tr>
                                                <td>{{$value->type->name}}</td>
                                                <td>{{$value->difference}}</td>
                                                <td>{{$value->accruals}}</td>
                                                <td>{{\Carbon\Carbon::parse($value->created_at)->format('d.m.Y')}}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6">
                                                <p class="text-center mt-100 mb-100">Не найдено ни одной записи</p>
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>

                            <div class="pull-right">
                                @php $get = $_GET; unset($get['page']) @endphp
                                {{$values->appends($get)->links()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection