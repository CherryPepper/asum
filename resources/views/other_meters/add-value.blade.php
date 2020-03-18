<div class="add-value">
    <form action="{{route('ometers.add_value.post')}}" method="post">
        <input type="hidden" name="user_id" value="{{$user_id}}">

        <div class="col-sm-2"></div>

        <div class="col-sm-8">
            @if(sizeof($meters) > 0)
                <p class="text-center">Внесение показаний на {{\Carbon\Carbon::parse($current_date)->format('d.m.Y')}}</p>

                <div class="row">
                    <div class="form-wrapper">
                        <label>Тип счетчика</label>

                        <div class="form-group">
                            <select class="form-control input-lg custom-select" name="type_id">
                                <option></option>
                                @foreach($meters as $meter)
                                    <option value="{{$meter->type->id}}">
                                        {{$meter->type->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-wrapper">
                        <label>Показание</label>

                        <div class="form-group">
                            <input type="text" class="form-control input-lg" name="value">
                        </div>
                    </div>
                </div>

                <div class="row text-center mt-20 mb-20">
                    <button type="submit" class="btn btn-info send-form-ajax">Сохранить показание </button>
                </div>
            @else
                <p class="text-center mt-100 mb-100">
                    Не найдено ни одного счетчика
                </p>
            @endif

        </div>
    </form>
</div>