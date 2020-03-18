<!DOCTYPE html>
<html>
    @include('layouts.elements.head', ['title' => $title])
<body>
    @yield('content')

    <div class="scripts">
        <script src="{{mix('/js/app.js')}}"></script>
        <script>
            setTimeout(function () {
                window.location = '';
            }, 500000);
        </script>
    </div>
</body>

</html>