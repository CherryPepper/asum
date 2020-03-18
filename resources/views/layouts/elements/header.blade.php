<!-- Header -->
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
        <div id="navbar" class="navbar-collapse">
            @include('layouts.elements.breadcrumbs', ['breadcrumbs' => $breadcrumbs])

            <ul class="nav navbar-nav navbar-right">

                @include('layouts.elements.notifications')

                <li>
                    <a href="{{route('logout')}}">
                        <i class="fa fa-sign-out"></i>
                    </a>
                </li>
                <li class="profile">
                    <a>
                        <div class="vcentered">
                            <p class="profile-name">{{$userInfo->first_name.' '.$userInfo->last_name}}</p>
                            <p class="profile-position">{{$userInfo->role->name}}</p>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- End of Header -->