<div class="table-responsive user-info">
    <table class="table table-sm">
        <tbody>

        @if(($meter->type_id == 1) && ($meter->tozelesh == 0))
        <tr>
            <td class="bg-gray">Пользователь</td>
            <td>{{$meter->user->first_name.' '.$meter->user->last_name}}</td>

            <td class="bg-gray">Договор</td>
            <td>№ {{$meter->user->contract}}</td>
        </tr>
        @endif
        <tr>
            <td class="bg-gray">Адрес</td>
            <td>{{\App\Helpers\Addresses::AdrString($meter->address)}}</td>

            <td class="bg-gray">Показание</td>
            <td>{{$meter->value}} Квт</td>
        </tr>
        <tr>
            <td class="bg-gray">Статус</td>
            <td>{{$meter->status->name}}</td>

            <td class="bg-gray">Тариф</td>
            <td>{{$meter->rate->title}}</td>
        </tr>
        @if($meter->status_id == 2)
            <tr>
                <td class="bg-gray">Отложен до</td>
                <td>{{\Carbon\Carbon::parse($meter->deferred_time)->format('d.m.Y H:i:s')}}</td>

                <td class="bg-gray">Убрать из отложенных</td>
                <td><a href="{{route('meter.removeFromDeferred', ['id' => $meter->id])}}" class="remove-from-deferred">Убрать</a></td>
            </tr>
        @endif
        @if($meter->parent_id > 0)
            <tr>
                <td colspan="4" class="text-center">
                    <a class="text-center reset-parent" href="{{route('meter.resetParent', ['id' => $meter->id])}}">
                        <i class="fa fa-refresh"></i> Сброс контрольного счетчика
                    </a>
                </td>
            </tr>
        @endif

        </tbody>
    </table>

    <form action="{{route('meter.edit.post')}}" method="post">
        {{csrf_field()}}
        <input type="hidden" name="id" value="{{$meter->id}}">

        <div class="col-xs-12">
            <div class="row">
                <div class="form-wrapper col-sm-6">
                    @if(!$errors->has('ip_address'))
                        <label>IP адрес</label>
                    @else
                        <label class="text-danger">{{$errors->first('ip_address')}}</label>
                    @endif

                    <div class="form-group {{$errors->has('ip_address') ? 'has-error' : ''}}">
                        <input type="text" class="form-control" name="ip_address" value="{{old('ip_address', $meter->ip_address)}}">
                    </div>
                </div>
                <div class="form-wrapper col-sm-6">
                    @if(!$errors->has('serial'))
                        <label>Серийный номер</label>
                    @else
                        <label class="text-danger">{{$errors->first('serial')}}</label>
                    @endif

                    <div class="form-group {{$errors->has('serial') ? 'has-error' : ''}}">
                        <input type="text" class="form-control" name="serial" value="{{old('serial', $meter->serial)}}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-wrapper col-sm-6">
                    @if(!$errors->has('login'))
                        <label>Логин счетчика</label>
                    @else
                        <label class="text-danger">{{$errors->first('login')}}</label>
                    @endif

                    <div class="form-group {{$errors->has('login') ? 'has-error' : ''}}">
                        <input type="text" class="form-control" name="login" value="{{old('login', $meter->login)}}">
                    </div>
                </div>
                <div class="form-wrapper col-sm-6">
                    @if(!$errors->has('password'))
                        <label>Пароль счетчика</label>
                    @else
                        <label class="text-danger">{{$errors->first('password')}}</label>
                    @endif

                    <div class="form-group {{$errors->has('password') ? 'has-error' : ''}}">
                        <input type="text" class="form-control" name="password" value="{{old('password', $meter->password)}}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-wrapper col-sm-6 edit-operator">
                    @if(!$errors->has('operator_id'))
                        <label>Оператор</label>
                    @else
                        <label class="text-danger">{{$errors->first('operator_id')}}</label>
                    @endif

                    <div class="form-group {{$errors->has('operator_id') ? 'has-error' : ''}}">
                        <select class="form-control custom-select" name="operator_id">
                            @foreach($operators as $operator)
                                <option value="{{$operator->id}}" @if(old('operator_id', $meter->operator_id) == $operator->id) selected @endif>{{$operator->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="form-group text-center">
                    <label class="checkbox">
                        <input name="meter_replacement" type="checkbox">
                        <span class="checkbox-placeholder"></span>
                        Замена счетчика
                    </label>
                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <button type="submit" class="btn btn-info send-form-ajax">Сохранить </button>
        </div>
    </form>

</div>