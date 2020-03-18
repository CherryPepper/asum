@extends('layouts.errors', ['title' => 'Ошибка 405'])

@section('content')
    <div class="center-vertical">
        <div class="center-content">

            <div class="col-md-6 center-margin">
                <div class="server-message wow bounceInDown inverse">
                    <h1>Ошибка 405</h1>
                    <h2>Недопустимый метод.</h2>
                    <p>Недопустимый метод для текущего запроса, пожалуйста вернитесь на главную и попробуйте заново. </p>

                    <a href="{{ route('home') }}" class="btn btn-lg btn-success">Вернуться на главную</a>
                </div>
            </div>

        </div>
    </div>
@endsection