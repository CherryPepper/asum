@extends('layouts.errors', ['title' => 'Ошибка 404'])

@section('content')
    <div class="center-vertical">
        <div class="center-content">

            <div class="col-md-6 center-margin">
                <div class="server-message wow bounceInDown inverse">
                    <h1>Ошибка 404</h1>
                    <h2>Страница не найдена.</h2>
                    <p>Запрашиваемая вами страница не найдена, возможно она была удалена или больше недоступна. </p>

                    <a href="{{ route('home') }}" class="btn btn-lg btn-success">Вернуться на главную</a>
                </div>
            </div>

        </div>
    </div>
@endsection