$(document).ready(function () {
    let modal = $('#modalAjax');

    $('#selectChilds').on('click', '.input-group', function () {
        let link = $(this).find('a.hidden');

        modal.modal('show', link);

        modal.css({'margin-top': 60})
    });

    $(document).on('shown.bs.modal', function () {
        let all_meters = modal.find('.select-meters .meter-popup');

        all_meters.popover({
            animation: false,
            html: true,
            placement: 'top',
            trigger: 'click',
            container: 'body'
        });
    });

    modal.on('click', '.select-meters .meter-popup', function () {
        let $this = $(this);
        let title = $this.data('serial');
        let content = $this.data('address');
        let all_meters = modal.find('.select-meters .meter-popup');
        let select_btn = modal.find('.select-meters-btn');

        all_meters.popover('hide');

        if(!$this.hasClass('active')){
            title += '<span class="popover-navigation"><a class="popover-close" title="Закрыть окно"><span class="ion ion-close-circled"></span></a></span>';
            content += '<hr class="mb-5"><span>Статус: '+$this.data('status')+'</span>';

            $this.data('bs.popover').options.title = title;
            $this.data('bs.popover').options.content = content;
            $this.popover('show');
        }

        $this.toggleClass('active');

        let active_size = modal.find('.select-meters .meter-popup.active').length;

        if(active_size > 0){
            select_btn.html('Выбрать ('+ active_size + ')');

            select_btn.removeClass('hidden');
        } else
            select_btn.addClass('hidden');
    });

    modal.on('click', '.select-meters-btn', function () {
        let $this = $(this);
        let parent = $this.closest('.select-meters');
        let active_meters = parent.find('.meter-popup.active');
        let serial_list = [];
        let id_list = [];
        let childs_block = $('#selectChilds');

        $.each(active_meters, function (i, elem) {
            serial_list.push($(elem).data('serial'));
            id_list.push($(elem).data('id'));
        });

        serial_list = serial_list.join(', ');
        id_list = id_list.join(',');

        childs_block.find('.input-lg').val(serial_list);
        childs_block.find('#childsList').val(id_list);

        modal.modal('toggle');
    });
});