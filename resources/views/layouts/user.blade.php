<!DOCTYPE html>
<html>
    @include('layouts.elements.head', ['title' => $title])
<body>
    <input type="hidden" id="MenuURI" value="{{$menu_uri}}">
    @include('layouts.elements.header', ['breadcrumbs' => $breadcrumbs])
    @include('layouts.elements.navigation')


    @yield('content')

    <div class="scripts">
        <script src="{{mix('/js/app.js')}}"></script>
        <script src="/js/pace.min.js"></script>

        <!-- Modal ajax -->
        <div class="modal fade" tabindex="-1" role="dialog" id="modalAjax">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <span class="header-text"></span>

                            <div class="panel-options">
                                <a class="panel-close" data-dismiss="modal">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </div>
                        <div class="panel-body">

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation -->
        <div class="modal fade" tabindex="-1" role="dialog" id="modalConfirmation">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="panel panel-danger">
                        <div class="panel-heading">
                            <span class="header-text"></span>

                            <div class="panel-options">
                                <a class="panel-close" data-dismiss="modal">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="col-sm-2"></div>
                            <div class="form-wrapper col-sm-8">
                                <label class="confirm-text"></label>

                                <div class="form-group" style="display: none">
                                    <input type="password" class="form-control input-lg" name="password">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger conf-btn">Продолжить</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Show toasts -->
        <script>
            $(document).ready(function () {
                @if (session('toast_messages'))
                    @foreach(session('toast_messages') as $message)
                        toastr.{{$message['status']}}('{{$message['message']}}');
                    @endforeach
                @endif
            });
        </script>

        <!-- Include JavaScript -->
        @yield('javascript')
    </div>
</body>

</html>