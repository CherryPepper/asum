<div class="meter-history">
    <h5>{{\App\Helpers\Addresses::AdrString($meter->address)}}</h5>

    @if(isset($date))
        <ul class="breadcrumb">
            @php
                $breadcrumbs = [
                    [
                        'current_frame' => 'month',
                        'current_title' => 'Месяц',
                        'prev_url' => route('meter.history', ['id' => $id, 'frame' => 'year']),
                        'prev_title' => 'Год',
                        'prev_value' => Carbon\Carbon::parse($date)->format('Y')
                    ],
                    [
                        'current_frame' => 'day',
                        'current_title' => 'Число',
                        'prev_url' => route('meter.history', ['id' => $id, 'frame' => 'month',
                                      'date' => Carbon\Carbon::parse($date)->format('Y')]),
                        'prev_title' => 'Месяц',
                        'prev_value' => $months[Carbon\Carbon::parse($date)->month]
                    ],
                    [
                        'current_frame' => 'hour',
                        'current_title' => 'Время',
                        'prev_url' => route('meter.history', ['id' => $id, 'frame' => 'day',
                                      'date' => Carbon\Carbon::parse($date)->format('Y-m')]),
                        'prev_title' => 'Число',
                        'prev_value' => Carbon\Carbon::parse($date)->format('d')
                    ],
                    [
                        'current_frame' => 'minute',
                        'current_title' => 'Точки',
                        'prev_url' => route('meter.history', ['id' => $id, 'frame' => 'hour',
                                      'date' => Carbon\Carbon::parse($date)->format('Y-m-d')]),
                        'prev_title' => 'Время',
                        'prev_value' => Carbon\Carbon::parse($date)->format('H:i')
                    ]
                ];
            @endphp

            @foreach($breadcrumbs as $breadcrumb)
                <li>
                    <a href="{{$breadcrumb['prev_url']}}">
                        {{$breadcrumb['prev_title']}}({{$breadcrumb['prev_value']}})
                    </a>
                </li>

                @if($current_frame == $breadcrumb['current_frame'])
                    <li>
                        {{$breadcrumb['current_title']}}
                    </li>
                @endif

                @break($current_frame == $breadcrumb['current_frame'])
            @endforeach
        </ul>
    @endif

    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th>Дата</th>
                <th>Потреблено</th>
                <th>Последнее показание</th>
            </tr>
            </thead>

            <tbody>
            @if(!empty($values->total()))
            @foreach($values as $value)
            <tr>
                <td>
                    @if($next_frame !== false)
                    @php
                        $dt = $date.$separator;
                        $dt .= ($value->point < 10) ? '0'.$value->point : $value->point;
                        ($current_frame == 'hour') ?  $dt .= ':00' : true;
                    @endphp

                    <a href="{{route('meter.history', ['id' => $id,'frame' => $next_frame, 'date' => $dt])}}" class="next-frame">
                        @if($current_frame == 'month')
                            {{$months[$value->point]}}
                        @elseif($current_frame == 'day')
                            {{\Carbon\Carbon::parse($value->time_point)->format('d.m.Y')}}
                        @elseif($current_frame == 'hour')
                            {{\Carbon\Carbon::parse($value->time_point)->format('H:00')}}
                        @else
                            {{$value->point}}
                        @endif
                    </a>
                    @else
                        {{\Carbon\Carbon::parse($value->time_point)->format('H:i')}}
                    @endif
                </td>
                <td>{{$value->difference}}</td>
                <td>{{$value->value}}</td>
            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="3">
                    <p class="text-center mt-100 mb-100">Показания отсутствуют</p>
                </td>
            </tr>
            @endif
            </tbody>
        </table>
    </div>

    <div class="pull-right">
        @php $get = $_GET; unset($get['page']);@endphp
        {{$values->appends($get)->links()}}
    </div>
</div>