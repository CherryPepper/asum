@extends('layouts.user', [
        'title' => 'Другие счетчики',
        'breadcrumbs' => [
            ['Другие счетчики'],
        ]
    ])

@section('content')
    <div class="content staff-report">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel">
                        <div class="panel-heading">
                            Другие счетчики
                        </div>
                        <div class="panel-body">
                            <form action="{{route('report.other_meters')}}">
                                <div class="row">
                                    <div class="form-wrapper col-sm-3">
                                        <label>Дата</label>

                                        <div class="form-group">
                                            <div class="input-group date">
                                                <input type="text" class="form-control input-lg" name="current_date" id="current-date"
                                                       value="{{\Carbon\Carbon::parse($current_date)->format('m.Y')}}">
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-wrapper col-sm-3">
                                        <label>Тип счетчика</label>

                                        <div class="form-group">
                                            <select class="form-control input-lg custom-select" name="type_id">
                                                <option></option>
                                                @foreach($meters as $meter)
                                                    <option value="{{$meter->id}}" @if($current_type == $meter->id) selected @endif>
                                                        {{$meter->name}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-wrapper col-sm-2">
                                            <label></label>
                                            <button type="submit" class="btn btn-info btn-block input-lg send-form mt-4">Применить</button>
                                        </div>
                                        <div class="form-wrapper col-sm-2">
                                            <label></label>
                                            <a type="submit" class="btn btn-default btn-block input-lg send-form mt-4" href="{{route('report.other_meters')}}">Очистить</a>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="v-spacing-xs"></div>

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover report">
                                    <thead>
                                    <tr>
                                        <th>Адрес</th>
                                        <th>Серийный номер</th>
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
                                                <td>{{\App\Helpers\Addresses::AdrString($value->user->meter->address)}}</td>
                                                <td>{{$value->meter->serial}}</td>
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