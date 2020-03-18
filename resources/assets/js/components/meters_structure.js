$(document).ready(function () {
    let page = $('.meters-structure');
    let all_meters = page.find('.meter-info');

    all_meters.popover({
        animation: false,
        html: true,
        placement: 'top',
        trigger: 'click',
        container: 'body'
    });

    page.on('click', '.meter-info', function () {
        let $this = $(this);
        let title = $this.data('serial');
        let content = $this.data('address');
        let id = $this.data('id');

        if($this.hasClass('active') && ($this.data('type') === 2))
            window.location = '/meters/structure/'+id;

        all_meters.removeClass('active');
        $this.addClass('active');

        all_meters.popover('hide');

        title += '<span class="popover-navigation">';
        if($this.data('type') === 4)
            title += '<a class="add-values" title="Добавить показания (для счетчиков без SIM)"><span class="fa fa-plus"></span></a>';

        title += '<a href="/meters/history/'+id+'?frame=year" class="meter-values" title="Показания счетчика"><span class="fa fa-bar-chart"></span></a>';
        title += '<a href="/meters/structure/0/'+id+'" class="move-meter" title="Перенос счетчика '+$this.data('serial')+'"><span class="fa fa-share-square"></span></a>';
        title += '<a href="/meters/edit/'+id+'" class="meter-settings" title="Редактирование счетчика"><span class="fa fa-gear"></span></a>';
        title += '<a class="popover-close" title="Закрыть окно"><span class="ion ion-close-circled"></span></a>';
        title += '</span>';

        content += '<hr class="mb-5">';
        content += '<span>Статус: '+$this.data('status')+'</span>';

        $this.data('bs.popover').options.title = title;
        $this.data('bs.popover').options.content = content;
        $this.popover('show');

    });

    $(document).on('click', '.popover-close', function () {
        $(this).closest('.popover.in').remove();
        all_meters.removeClass('active');
    });


    $(document).on('click', function (e) {
        let meter_info = $('.meter-info, .meter-popup');
        let popover = $('.popover.in');

        if((meter_info.has(e.target).length === 0) && (popover.has(e.target).length === 0)){
            popover.remove();
            all_meters.removeClass('active');
        }
    });

    $(document).on('click', '.popover-navigation a', function () {
        let modal = $('#modalAjax');
        let $this = $(this);

        if($this.hasClass('popover-close'))
            return false;
        $('.popover.in').remove();
        all_meters.removeClass('active');

        modal.modal('show', $this);

        return false;
    });
});