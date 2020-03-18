@extends('layouts.auth', ['title' => 'Страница авторизации'])

@section('content')
    <div class="content" id="login-page">
        <div class="container-fluid">
            <div class="panel" id="login-panel">
                <div class="panel-heading">
                    <i class="fa fa-unlock-alt vcentered"></i>
                    <div class="vcentered">
                        <h3> Здравствуйте </h3>
                        <h5> Пожалуйста авторизуйтесь:</h5>
                    </div>
                </div>
                <div class="panel-body">
                    <form class="row" method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}
                        <div class="form-wrapper col-sm-6 {{ $errors->has('login') ? ' has-error' : '' }}">
                            <label for="Login">Логин</label>
                            <div class="form-group">
                                <input class="form-control" id="Login" value="{{ old('login') }}" name="login" placeholder="user" autofocus>

                                @if ($errors->has('login'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('login') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-wrapper col-sm-6 {{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="Password">Пароль</label>
                            <div class="form-group">
                                <input type="password" class="form-control" id="Password" name="password" placeholder="******">

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-wrapper col-sm-12">
                            <div class="form-group">
                                <label class="checkbox" id="checkbox-login">
                                    <input name="remember" type="checkbox" {{ old('remember') ? 'checked' : '' }}>
                                    <span class="checkbox-placeholder"></span>
                                    Запомнить меня
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-lg btn-info btn-block">
                            Войти
                        </button>
                    </form>

                    {{--<a href="{{ route('password.request') }}" class="text-center btn-block no-margin-bottom" id="not-a-member"> Забыли свой пароль? </a>--}}
                </div>
            </div>
        </div>
    </div>
@endsection