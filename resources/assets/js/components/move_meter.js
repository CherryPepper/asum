$(document).ready(function () {
    let modal = $('#modalAjax');

    modal.on('click', '.meter-info', function () {
        let $this = $(this);
        let id = $this.data('id');
        let all_meters = modal.find('.meter-info');
        let move = $('#moveId').val();

        if($this.hasClass('active')){
            $.get('/meters/structure/'+id+'/'+move, function (data) {
                modal.find('.panel-body').html(data);
            });

            return false;
        }

        all_meters.removeClass('active');
        $this.addClass('active');
    });

    modal.on('click', '.move-meter-btn', function () {
        let $this = $(this);
        let post = {};

        if(!$this.hasClass('disabled')){
            $this.addClass('disabled');

            post.id = $('#moveId').val();
            post.moveTo = modal.find('.meter-info.active').data('id');

            $.post('/meters/move', post,function (data) {
                if(data.status === 'success'){
                    toastr.success(data.message);
                    modal.modal('toggle');

                    setTimeout(function () {
                        window.location = '';
                    }, 1000);
                }
                else
                    toastr.error('Неизвестная ошибка')
            });
        }

        return false;
    });

    modal.on('click', '.breadcrumb a', function () {
        let $this = $(this);
        let move = $('#moveId').val();
        let url = $this.attr('href');

        if(move !== undefined)
            url += '/'+move;

        $.get(url, function (data) {
            modal.find('.panel-body').html(data);
        });

        return false;
    });

    $(document).on('click', function (e) {
        let meter_info = $('.meter-info');
        let all_meters = modal.find('.meter-info');
        let move_btn = $('.move-meter-btn');

        if((meter_info.has(e.target).length === 0)){
            all_meters.removeClass('active');
            move_btn.hide();
        }
        else{
            move_btn.find('span').html($(e.target).closest('.meter-info').find('.serial').text());
            move_btn.show();
        }
    });
});