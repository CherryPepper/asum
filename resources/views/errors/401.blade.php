@extends('layouts.errors', ['title' => 'Ошибка 401'])

@section('content')
    <div class="center-vertical">
        <div class="center-content">

            <div class="col-md-6 center-margin">
                <div class="server-message wow bounceInDown inverse">
                    <h1>Ошибка 401</h1>
                    <h2>Недостаточно прав</h2>
                    <p>Извините, у вас нет прав для доступа к этой странице.</p>

                    <p><a href="{{ route('home') }}" class="btn btn-lg btn-success">Вернуться на главную</a></p>
                </div>
            </div>

        </div>
    </div>
@endsection