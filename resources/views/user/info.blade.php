@extends('layouts.user', [
        'title' => 'Общая информация',
        'breadcrumbs' => [
            ['Общая информация']
        ]
    ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <div class="panel">
                        <div class="panel-heading">
                            Общая информация
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive user-info">
                                <table class="table table-sm">
                                    <tbody>
                                    <tr>
                                        <td class="bg-gray">Договор</td>
                                        <td>№ {{$user->contract}}</td>

                                        <td class="bg-gray">Статус счетчика</td>
                                        <td>{{$user->meter->status->name}}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray">Имя</td>
                                        <td>{{$user->first_name. ' '. $user->last_name}}</td>

                                        <td class="bg-gray">Серийный номер</td>
                                        <td>{{$user->meter->serial}}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray">E-mail</td>
                                        <td>{{$user->email}}</td>

                                        <td class="bg-gray">IP адрес</td>
                                        <td>{{$user->meter->ip_address}}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray">Адрес</td>
                                        <td>{{\App\Helpers\Addresses::AdrString($user->meter->address)}}</td>

                                        <td class="bg-gray">Тариф</td>
                                        <td>{{$user->meter->rate->title}}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray">Паспорт</td>
                                        <td>{{$user->passport.' - '.$user->passport_mvd}}</td>

                                        <td class="bg-gray">Текущее показание</td>
                                        <td>{{$user->meter->value}} Квт</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray">Баланс</td>
                                        <td>0 руб</td>

                                        <td class="bg-gray"></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="bg-gray">Дата добавления</td>
                                        <td>{{\Carbon\Carbon::parse($user->created_at)->format('d.m.Y')}}</td>

                                        <td class="bg-gray"></td>
                                        <td></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="panel">
                        <div class="panel-heading">
                            Последние показания
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover report">
                                    <thead>
                                    <tr>
                                        <th>Месяц</th>
                                        <th>Показание</th>
                                        <th>Потребление</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($values))
                                        @foreach($values as $value)
                                            <tr>
                                                <td>{{$months[$value->point]}}</td>
                                                <td>{{$value->value}}</td>
                                                <td>{{$value->difference}}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3">
                                                <p class="text-center mt-100 mb-100">Показания отсутствуют</p>
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection