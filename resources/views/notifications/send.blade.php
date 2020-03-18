@extends('layouts.user', [
        'title' => 'Рассылка уведомлений',
        'breadcrumbs' => [
            ['Рассылка уведомлений']
        ]
    ])

@section('content')
    <div class="content add-control">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-5">
                    <div class="panel">
                        <div class="panel-heading">
                            Отправка уведомления
                        </div>
                        <div class="panel-body">
                            <form action="{{route('notifications.send.post')}}" method="post">
                                {{csrf_field()}}

                                <div class="col-sm-1"></div>

                                <div class="col-sm-10">
                                    <div class="row">
                                        <div class="form-wrapper">
                                            @if(!$errors->has('title'))
                                                <label>Заголовок</label>
                                            @else
                                                <label class="text-danger">{{$errors->first('title')}}</label>
                                            @endif

                                            <div class="form-group {{$errors->has('title') ? 'has-error' : ''}}">
                                                <input type="text" class="form-control input-lg" name="title" value="{{old('title')}}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-wrapper">
                                            @if(!$errors->has('message'))
                                                <label>Сообщение</label>
                                            @else
                                                <label class="text-danger">{{$errors->first('message')}}</label>
                                            @endif

                                                <div class="form-group {{$errors->has('message') ? 'has-error' : ''}}">
                                                    <textarea class="form-control" name="message" placeholder="Напишите сообщение..." style="height: 150px;">{{old('message')}}</textarea>
                                                </div>
                                        </div>
                                    </div>

                                    <div class="row mt-20">
                                        <div class="col-sm-12 text-center">
                                            <button type="submit" class="btn btn-info input-lg send-form">Отправить уведомление</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-sm-7">
                    <div class="panel">
                        <div class="panel-heading">
                            История уведомлений
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover report">
                                    <thead>
                                    <tr>
                                        <th>Заголовок</th>
                                        <th>Сообщение</th>
                                        <th>Дата отправки</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($notifications->total()))
                                        @foreach($notifications as $notification)
                                            <tr>
                                                <td>{{$notification->title}}</td>
                                                <td>{{$notification->message}}</td>
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