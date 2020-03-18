@if(in_array($userInfo->role_id, [1,4]))
    <li class="dropdown" id="notifications-toggle">
        <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="#">
            <i class="fa fa-bell"></i>
            <span class="label label-danger notification-label">{{$new_notifications_cnt}}</span>
        </a>
        <ul class="list-unstyled notifications dropdown-menu">
            @if(sizeof($new_notifications) > 0)
                @foreach($new_notifications as $notification)
                    <li>
                        <span class="notification-title vcentered">
                            <strong> {{str_limit($notification->title, 25)}} </strong>
                            <br>
                            <span>{{str_limit($notification->message, 50)}}</span>
                        </span>
                        <span class="notification-time text-muted">
                            {{\Carbon\Carbon::parse($notification->created_at)->format('d.m.Y')}}
                        </span>
                    </li>
                @endforeach
            @else
                <li>
                    <p class="text-center mt-40 mb-40">Нет новых уведомлений</p>
                </li>
            @endif

            <li class="text-center">
                <a href="{{route('user.notifications')}}">Все уведомления</a>
            </li>
        </ul>
    </li>
@endif