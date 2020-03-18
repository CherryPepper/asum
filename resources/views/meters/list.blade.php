@extends('layouts.user', [
        'title' => 'Регистрация счетчиков',
        'breadcrumbs' => [
            ['Регистрация счетчиков'],
        ]
    ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel">
                        <div class="panel-heading">
                            Список счетчиков для регистрации
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive tr-link">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Адрес</th>
                                        <th>ФИО</th>
                                        <th>Номер договора</th>
                                        <th>Дата добавления</th>
                                        <th>Тариф</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($meters->total()))
                                        @foreach($meters as $meter)
                                            <tr>
                                                <td>{{$meter->id}}</td>
                                                <td>{{\App\Helpers\Addresses::AdrString($meter->address)}}</td>
                                                <td>{{$meter->user->first_name.' '.$meter->user->last_name}}</td>
                                                <td>{{$meter->user->contract}}</td>
                                                <td>{{\Carbon\Carbon::parse($meter->created_at)->format('d.m.Y')}}</td>
                                                <td>{{$meter->rate->title}}</td>
                                                <td class="form-actions-icons">
                                                    <div class="actions" style="right: 60px">
                                                        <a href="{{route('meters.registration.id', ['id' => $meter->id])}}"
                                                           data-toggle="tooltip" class="mr-10 block-link" title="Зарегистрировать">
                                                            <span class="fa fa-angle-right" aria-hidden="true"></span>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7">
                                                <p class="text-center mt-100 mb-100">Не найдено счетчиков для регистрации</p>
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>

                            <div class="pull-right">
                                {{$meters->links()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection