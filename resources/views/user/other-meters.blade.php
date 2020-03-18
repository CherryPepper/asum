@extends('layouts.user', [
        'title' => 'Другие счетчики',
        'breadcrumbs' => [
            ['Другие счетчики']
        ]
    ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-5">
                    <div class="panel">
                        <div class="panel-heading">
                            Внесение показаний на {{\Carbon\Carbon::parse($current_date)->format('d.m.Y')}}
                        </div>
                        <div class="panel-body">
                            @if(sizeof($meters) > 0)
                                <form action="{{route('user.other_meters.post')}}" method="post">
                                    {{csrf_field()}}

                                    <div class="col-sm-1"></div>

                                    <div class="col-sm-10">
                                        <div class="row">
                                            <div class="form-wrapper">
                                                @if(!$errors->has('type_id'))
                                                    <label>Тип счетчика</label>
                                                @else
                                                    <label class="text-danger">{{$errors->first('type_id')}}</label>
                                                @endif

                                                <div class="form-group {{$errors->has('type_id') ? 'has-error' : ''}}">
                                                    <select class="form-control input-lg custom-select" id="type-id" name="type_id">
                                                        <option></option>
                                                        @foreach($meters as $meter)
                                                            <option value="{{$meter->type->id}}" {{old('type_id') == $meter->type->id ? 'selected' : ''}}>
                                                                {{$meter->type->name}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-wrapper">
                                                @if(!$errors->has('value'))
                                                    <label>Показание</label>
                                                @else
                                                    <label class="text-danger">{{$errors->first('value')}}</label>
                                                @endif

                                                <div class="form-group {{$errors->has('value') ? 'has-error' : ''}}">
                                                    <input type="text" class="form-control input-lg" name="value" value="{{old('value')}}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-20">
                                            <div class="col-sm-12 text-center">
                                                <button type="submit" class="btn btn-info input-lg send-form">Сохранить показание</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            @else
                                <p class="text-center mt-100 mb-100">
                                    Не найдено ни одного счетчика
                                </p>
                            @endif

                        </div>
                    </div>
                </div>

                <div class="col-sm-7">
                    <div class="panel">
                        <div class="panel-heading">
                            Последние показания
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover report">
                                    <thead>
                                    <tr>
                                        <th>Тип</th>
                                        <th>Показание</th>
                                        <th>Потребление</th>
                                        <th>Начислено руб</th>
                                        <th>Дата</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($values->total()))
                                        @foreach($values as $value)
                                            <tr>
                                                <td>{{$value->meter->type->name}}</td>
                                                <td>{{$value->value}}</td>
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