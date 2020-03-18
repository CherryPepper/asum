<ul class="breadcrumb">
    <li class="breadcrumb-home">
        <a href="{{route('home')}}">
            <i class="fa fa-home"></i>
        </a>
    </li>

    @foreach($breadcrumbs as $breadcrumb)
        <li @if($breadcrumb == end($breadcrumbs)) class="active" @endif>
            <a href="{{isset($breadcrumb[1]) ? $breadcrumb[1] : '#'}}" >
                {{$breadcrumb[0]}}
            </a>
        </li>
    @endforeach
</ul>