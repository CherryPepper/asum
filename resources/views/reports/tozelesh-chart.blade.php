@section('javascript')
<script type="text/javascript">
    let tz_chart = echarts.init(document.getElementById('tozelesh-chart')),
        serials = [],
        dates = [],
        values = [],
        tmp_values = [];

    @foreach($meters as $meter)
        tmp_values = [];
        serials.push('{{$meter->serial}}');

        @foreach($meter->values as $value)
            tmp_values.push({{(float)$value->difference}});
        @endforeach

        values.push({
            name: '{{$meter->serial}}',
            type:'line',
            data: tmp_values
        });
    @endforeach

    @foreach($dates as $date)
        dates.push('{{\App\Helpers\DateTime::getDateForConsumptionReport($current_frame, $date, $all_months)}}');
    @endforeach

    let option = {
        tooltip: {
            trigger: 'axis'
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            top: '10%'
        },
        legend: {
            data: serials
        },
        xAxis: {
            data: dates
        },
        yAxis: {
            type: 'value'
        },
        series: values
    };

    // use configuration item and data specified to show chart
    tz_chart.setOption(option);
</script>
@endsection

<div id="tozelesh-chart" style="width: 100%;height: 700px;"></div>