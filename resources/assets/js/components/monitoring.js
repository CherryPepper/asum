$(document).ready(function () {
    let page = $('.monitoring');

    page.on('click', '.show-missed-points', function () {
        let modal = $('#modalAjax');
        let $this = $(this);

        $this.attr('title', $this.data('original-title'));

        modal.modal('show', $this);
        return false;
    });

    $('.monitoring.missed-points').on('click', 'table tbody tr', function () {
        $(this).find('.show-missed-points').click();
    });
});