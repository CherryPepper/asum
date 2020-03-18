$(document).ready(function () {
    let page = $('.add-total-value');

    page.on('change', '#type-id', function () {
        let $this = $(this);
        let id = parseInt($this.val());
        let form = $this.closest('form');
        let price_field = form.find('#price');

        if(id)
            price_field.val(form.find('#price-'+id).val());
        else
            price_field.val('')
    });

    page.on('click', '.change-price', function () {
        page.find('#price').prop('readonly', false).focus();

        return false;
    });
});