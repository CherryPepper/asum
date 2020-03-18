<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th>Серийный номер</th>
            <th>Адрес</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @if(!empty($meters->total()))
            @foreach($meters as $meter)
                <tr>
                    <td>{{$meter->serial}}</td>
                    <td>{{\App\Helpers\Addresses::AdrString($meter->address)}}</td>
                    <td class="form-actions-icons">
                        <div class="actions">
                            <a href="{{route('monitoring.missed.points', ['id' => $meter->id])}}" data-toggle="tooltip" title="Пропушенные точки" class="show-missed-points">
                                <span class="glyphicon glyphicon-list" style="color: #3097D1"></span>
                            </a>
                        </div>
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