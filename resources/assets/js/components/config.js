$(document).ready(function () {
    let csrf_token = $('meta[name="csrf-token"]').attr('content');

    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "hideEasing": "linear",
    };

    window.axios.defaults.headers.common = {
        'X-CSRF-TOKEN': csrf_token,
        'X-Requested-With': 'XMLHttpRequest'
    };
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrf_token
        },
        statusCode: {
            422: function(data) {
                $.each(data.responseJSON, function (i, message) {
                    toastr.error(message);
                });
            }
        }
    });
});