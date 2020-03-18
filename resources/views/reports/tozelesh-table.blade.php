<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover report">
        <thead>
        <tr>
            <th class="text-center" style="width: 15%">Адрес</th>
            <th class="text-center" style="width: 15%">Серийный номер</th>
            <th class="text-center">Потребление КВТ</th>
            <th class="text-center" style="width: 10%">Всего КВТ</th>
        </tr>
        </thead>
        <tbody>

        @if(!empty($meters->total()))
            @foreach($meters as $meter)
                <tr>
                    <td>{{\App\Helpers\Addresses::AdrString($meter->address)}}</td>
                    <td>{{$meter->serial}}</td>
                    <td>
                        @php $total_difference = 0; @endphp
                        @foreach($meter->values as $value)
                            <div class="col-sm-3">
                                <strong>{{\App\Helpers\DateTime::getDateForConsumptionReport($current_frame, $value->time_point, $all_months)}}</strong> -
                                {{(float)$value->difference}}
                            </div>

                            @php $total_difference += $value->difference; @endphp
                        @endforeach
                    </td>
                    <td class="text-center">
                        {{(float)$total_difference}}
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="6">
                    <p class="text-center mt-100 mb-100">Не найдено ни одной записи</p>
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>