$(document).ready(function () {
    let page = $('.consumption-report');
    let time_picker_blocks = $('#time-from, #time-to');
    let date_picker_blocks = $('#start-date, #end-date');
    let frame = parseInt($('#frameId').val());

    if(frame > 0)
        changeFrame(frame);

    date_picker_blocks.datetimepicker({
        useCurrent: false,
        locale: 'ru',
        viewMode: 'months',
        format: 'DD.MM.YYYY'
    });

    time_picker_blocks.datetimepicker({
        useCurrent: false,
        locale: 'ru',
        format: 'HH:00'
    }).on("dp.show", function () {
        let $this = $(this);
        $this.closest('.form-group').find('.bootstrap-datetimepicker-widget').find('.timepicker-hour').click();
    }).on('dp.change', function () {
        let $this = $(this);
        let first_val = parseInt(page.find('#time-from').val());
        let last_val = parseInt(page.find('#time-to').val());

        if(first_val > last_val){
            toastr.error('Начальная точка больше конечной');

            $('#time-from').val('');
            $('#time-to').val('');
        }

        $this.datetimepicker('hide');
    });

    $("#start-date").on("dp.change", function (e) {
        let date_end = $('#end-date');

        if(date_end.data("DateTimePicker") !== undefined)
            date_end.data("DateTimePicker").minDate(e.date);
    });
    $("#end-date").on("dp.change", function (e) {
        let date_start = $('#start-date');

        if(date_start.data("DateTimePicker") !== undefined)
            date_start.data("DateTimePicker").maxDate(e.date);
    });

    page.on('change', '.frame', function () {
        let $this = $(this);
        let frame = parseInt($this.val());

        changeFrame(frame);
    });

    function changeFrame(frame) {
        if(date_picker_blocks.data("DateTimePicker") !== undefined)
            date_picker_blocks.datetimepicker('destroy');

        switch (frame){
            case 1:
            {
                date_picker_blocks.datetimepicker({
                    useCurrent: false,
                    locale: 'ru',
                    viewMode: 'months',
                    format: 'DD.MM.YYYY'
                });

                $('.time-picker').hide();
                $('#end-date').prop('disabled', false);
                break;
            }
            case 2:
            {
                date_picker_blocks.datetimepicker({
                    useCurrent: false,
                    locale: 'ru',
                    viewMode: 'days',
                    format: 'DD.MM.YYYY'
                });

                $('.time-picker').hide();
                $('#end-date').prop('disabled', false);
                break;
            }
            case 3:
            case 4:
            {
                date_picker_blocks.datetimepicker({
                    useCurrent: false,
                    locale: 'ru',
                    viewMode: 'days',
                    format: 'DD.MM.YYYY'
                });

                $('.time-picker').show();
                $('#end-date').prop('disabled', true);
                break;
            }
        }
    }
});