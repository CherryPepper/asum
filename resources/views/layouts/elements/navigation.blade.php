<!-- Navigation -->
<div class="navigation">
    <a href="{{route('home')}}" class="navbar-brand">
        <span class="vcentered">АСУМ 2.0</span>
        <i class="fa fa-bars text-primary left-nav-toggle pull-right vcentered"></i>
    </a>

    <ul class="nav primary">
        @foreach($navigation as $nav)
            @php
                if(isset($nav[$nav['id']]))
                    $uri_in_arr = array_search($menu_uri, array_column($nav[$nav['id']], 'url'));
                else
                    $uri_in_arr = ($nav['url'] == $menu_uri) ? true : false;
            @endphp
            <li class="nav-item-expandable @if($uri_in_arr !== false) active @endif">
                <a href="{{$nav['url']}}" {{isset($nav[$nav['id']]) ? 'data-toggle=collapse' : ''}} aria-expanded="false">
                    <i class="{{$nav['ico']}}"></i>
                    <span> {{$nav['title']}} </span>

                    @if(isset($nav[$nav['id']]))
                        <span class="nav-item-icon"> <i class="fa fa-chevron-down"></i> </span>
                    @endif
                </a>

                @if(isset($nav[$nav['id']]))
                    <ul id="{{$nav['id']}}" class="nav nav-item collapse @if($uri_in_arr !== false) in @endif">
                        @foreach($nav[$nav['id']] as $sub)
                            <li @if($menu_uri == $sub['url']) class="active" @endif>
                                <a href="{{$sub['url']}}">
                                    <i class="{{$sub['ico']}}"></i>
                                    <span> {{$sub['title']}} </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>

    <div class="time text-center">
        <h5 class="current-time2">&nbsp;{{\Carbon\Carbon::now()->format('H:i')}} </h5>
        <h5 class="current-time">&nbsp;{{\Carbon\Carbon::now()->format('M Y')}} </h5>
    </div>
</div>
<!-- End of Navigation -->