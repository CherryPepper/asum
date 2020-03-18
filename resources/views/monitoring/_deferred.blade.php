<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th>Серийный номер</th>
            <th>Адрес</th>
            <th>Не ответил раз</th>
            <th>Отложен до</th>
            <th>Вернуть</th>
        </tr>
        </thead>
        <tbody>
        @if(!empty($meters->total()))
            @foreach($meters as $meter)
                <tr>
                    <td>{{$meter->serial}}</td>
                    <td>{{\App\Helpers\Addresses::AdrString($meter->address)}}</td>
                    <td>{{$meter->not_response_cnt}}</td>
                    <td>{{\Carbon\Carbon::parse($meter->deferred_time)->format('d.m.Y H:i:s')}}</td>
                    <td>
                        <a href="{{route('meter.removeFromDeferred', ['id' => $meter->id])}}" class="remove-from-deferred" data-reload="1">
                            <span class="glyphicon glyphicon-ok text-success"></span>
                        </a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="5">
                    <p class="text-center mt-100 mb-100">Не найдено ни одного счетчика</p>
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>