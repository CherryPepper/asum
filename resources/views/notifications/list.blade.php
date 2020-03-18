@extends('layouts.user', [
        'title' => 'Уведомления',
        'breadcrumbs' => [
            ['Уведомления']
        ]
    ])

@section('content')
    <div class="content add-control">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel">
                        <div class="panel-heading">
                            История уведомлений
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Заголовок</th>
                                        <th>Сообщение</th>
                                        <th>Дата</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($notifications->total()))
                                        @foreach($notifications as $notification)
                                            <tr>
                                                <td>{{$notification->title}}</td>
                                                <td>{!! $notification->message !!}</td>
                                                <td>{{\Carbon\Carbon::parse($notification->created_at)->format('d.m.Y')}}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3">
                                                <p class="text-center mt-100 mb-100">Уведомления не найдены</p>
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>

                            <div class="pull-right">
                                @php $get = $_GET; unset($get['page']) @endphp
                                {{$notifications->appends($get)->links()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection