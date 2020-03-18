$(document).ready(function () {

    $('[data-toggle="tooltip"], .toggle-block').tooltip({
        placement: "top",
        delay: {show: 200, hide: 100},
        trigger: 'hover'
    });

    $('#modalConfirmation').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget);
        let url = button.data('url');
        let id = button.data('id');
        let modal = $(this);
        let cnf_btn = modal.find('.modal-footer button.conf-btn');
        let panel = modal.find('.panel');

        panel.find('.header-text').text(button.data('header-text'));
        panel.find('.confirm-text').text(button.data('confirm-text'));
        panel.attr('class', '').addClass('panel').addClass('panel-'+button.data('type'));
        cnf_btn.attr('class', '').addClass('btn').addClass('conf-btn').addClass('btn-'+button.data('type'));
        cnf_btn.attr('data-url', url).attr('data-id', id).addClass(button.data('method'));

        if(button.data('method') === 'delete-confirm')
            panel.find('.form-group').show();
        else
            panel.find('.form-group').hide();
    });

    $('#modalAjax').on('show.bs.modal', function (event) {
        let $this = $(event.relatedTarget);
        let modal = $('#modalAjax');
        let panel = modal.find('.panel');

        panel.find('.header-text').text($this.attr('title'));

        $.get($this.attr('href'), function (data) {
            panel.find('.panel-body').html(data);

            panel.find('.panel-body .custom-select').selectize({
                sortField: 'text'
            });

            panel.find('.panel-body .address-search').selectize(address_search_settings);
        });
    });

    $(document).on('click', '#modalAjax .next-frame, ' +
        '#modalAjax .pagination a', function () {
        let $this = $(this);

        $.get($this.attr('href'), function (data) {
            $('#modalAjax').find('.panel-body').html(data);
        });
        return false;
    });

    $('.custom-select').selectize({
        sortField: 'text'
    });

    $(document).on('click', '.send-form', function () {
        $(this).prop('disabled', true);
        $(this).closest('form').submit();
    });

    $(document).on('click', '.send-form-ajax', function () {
        let $this = $(this);
        let form = $this.closest('form');

        $this.prop('disabled', true).append('<i class="fa fa-refresh fa-spin"></i>');
        $.post(form.attr('action'), form.serialize(), function (data) {

            $this.prop('disabled', false);
            $this.find('i').remove();

            if(data.status === 'success'){
                toastr.success(data.message);
                $('#modalAjax').modal('toggle');

            }
            else if(data.status === 'error')
                toastr.error(data.message);

        }).statusCode({
            422: function (data) {
                $('form label').removeClass('text-danger');
                $('form div.form-group').removeClass('has-error');

                $.each(data.responseJSON, function (i) {
                    let input = $('*[name="'+i+'"]');
                    let parent = input.closest('.form-wrapper');

                    parent.find('label').addClass('text-danger');
                    parent.find('div.form-group').addClass('has-error');
                });

                $this.prop('disabled', false);
                $this.find('i').remove();
            }
        });

        return false;
    });

    let modal = $('.modal');

    modal.on('click', 'button.delete-confirm', function () {
        let $this = $(this);
        let parent = $this.closest('.modal');
        let password = parent.find('input[type="password"]').val();
        let close_btn = parent.find('.panel-close');

        $.post($this.attr('data-url'), {
            password: password,
            id: $this.attr('data-id')
        }, function (data) {
            if(data.status === 'success'){
                close_btn.click();
                toastr.success('Запись успешно удалена');

                if(data.url !== undefined){
                    setTimeout(function () {
                        window.location = data.url;
                    }, 2000)
                }
                if(data.clearMap !== undefined){
                    let id = data.clearMap.object_id,
                        objects_block = $('.hidden #objects');

                    $.each(tz_map_meters[id]['lamps'], function (i, lamp) {
                        lamp.setMap(null);
                    });
                    tz_map_meters[id].setMap(null);

                    objects_block.find('.meters[data-id="'+id+'"]').remove();
                    objects_block.find('.lamps-'+id).remove();
                }
            }
        });
    });

    modal.on('click', 'button.get', function () {
        window.location = $(this).data('url');
    });

    $('#date-from, #date-to').datetimepicker({
        useCurrent: false,
        locale: 'ru',
        viewMode: 'days',
        format: 'DD.MM.YYYY'
    });

    $('#datetime-from, #datetime-to').datetimepicker({
        useCurrent: true,
        locale: 'ru',
        viewMode: 'days',
        format: 'DD.MM.YYYY HH:mm'
    });

    $("#date-from").on("dp.change", function (e) {
        $('#date-to').data("DateTimePicker").minDate(e.date);
    });
    $("#date-to").on("dp.change", function (e) {
        $('#date-from').data("DateTimePicker").maxDate(e.date);
    });

    $('.hour-from, .hour-to').datetimepicker({
        format: 'HH:00'
    });

    $("#current-date").datetimepicker( {
        viewMode: 'months',
        format: 'MM.YYYY',
        locale: 'ru'
    });

    $('.tr-link').on('click', 'tr', function () {
        window.location = $(this).find('a.block-link').attr('href');
    });

    $(document).on('click', '.remove-from-deferred', function () {
        let $this = $(this);
        let url = $this.attr('href');
        let reload_page = ($this.data('reload') === 1);

        $this.addClass('disabled');
        $this.attr('href', '');

        $.get(url, function (data) {
            if(data.status === 'success'){
                toastr.success(data.message);

                if(reload_page){
                    $('#modalAjax').modal('hide');

                    setTimeout(function () {
                        window.location = '';
                    }, 1000);
                }
                else
                    $('#modalAjax').modal('toggle');
            }
            else
                toastr.error('Неизвестная ошибка')
        });

        return false;
    });

    $(document).on('click', '.reset-parent', function () {
        let $this = $(this);
        let url = $this.attr('href');

        $this.addClass('disabled');
        $this.attr('href', '');

        $.get(url, function (data) {
            if(data.status === 'success'){
                toastr.success(data.message);
                $('#modalAjax').modal('toggle');

                setTimeout(function () {
                    window.location = '';
                }, 1000);
            }
            else
                toastr.error('Неизвестная ошибка')
        });

        return false;
    });

    $('.create-client, .edit-client').on('click', '.meter input[type="checkbox"]', function () {
        let $this = $(this);
        let meter = $this.closest('.meter');

        if($this.is(':checked')){
            meter.find('.meter-fields').css({'opacity': 1});
            meter.find('.meter-fields input').prop('disabled', false);
        }else{
            meter.find('.meter-fields').css({'opacity': 0.5});
            meter.find('.meter-fields input').prop('disabled', true);
        }
    });

    $(document).on('click', '.open-popup', function () {
        let modal = $('#modalAjax');

        modal.modal('show', $(this));

        return false;
    });
});