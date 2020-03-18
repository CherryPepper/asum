<!DOCTYPE html>
<html>
    @include('layouts.elements.head', ['title' => $title])

    <body>
        <style type="text/css">
            html,body {
                height: 100%;
            }
            body {
                background: #fff;
                overflow: hidden;
            }
        </style>

        <img src="/images/blurred-bg.jpg" class="login-img wow fadeIn">

        @yield('content')

        <div class="scripts">
            <script src="{{mix('/js/app.js')}}"></script>

            <script type="text/javascript">
                /* WOW animations */

                wow = new WOW({
                    animateClass: 'animated',
                    offset: 100
                });
                wow.init();
            </script>
        </div>
    </body>
</html>