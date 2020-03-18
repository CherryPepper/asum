@extends('layouts.errors', ['title' => 'Ошибка 500'])

@section('content')
    <div class="center-vertical">
        <div class="center-content">

            <div class="col-md-6 center-margin">
                <div class="server-message wow bounceInDown inverse">
                    <h1>Ошибка 500</h1>
                    <h2>Ошибка на стороне сервера.</h2>
                    <p>Сервер не смог правильно обработать ваш запрос. Пожалуйста попробуйте позже или свяжитесь с администратором.</p>

                    <p><a href="{{ route('home') }}" class="btn btn-lg btn-success">Вернуться на главную</a></p>
                </div>
            </div>

        </div>
    </div>
@endsection