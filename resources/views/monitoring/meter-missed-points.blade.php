<div class="table-responsive">
    <p class="text-center">Всего <span class="badge">{{$points->total()}}</span></p>
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th>Серийный номер</th>
            <th>Время</th>
        </tr>
        </thead>
        <tbody>
        @if(!empty($points->total()))
            @foreach($points as $point)
                <tr>
                    <td>{{$point->meter->serial}}</td>
                    <td>{{$point->point}}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="5">
                    <p class="text-center mt-100 mb-100">Не найдено ни одной точки</p>
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

<div class="pull-right">
    {{$points->links()}}
</div>