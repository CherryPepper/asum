<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th>Серийный номер</th>
            <th>Адрес</th>
            <th>Статус</th>
        </tr>
        </thead>
        <tbody>
        @if(!empty($meters->total()))
            @foreach($meters as $meter)
                <tr>
                    <td>{{$meter->serial}}</td>
                    <td>{{\App\Helpers\Addresses::AdrString($meter->address)}}</td>
                    <td>
                        <span class="fa fa-circle text-success"></span>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="3">
                    <p class="text-center mt-100 mb-100">Не найдено ни одного счетчика</p>
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>