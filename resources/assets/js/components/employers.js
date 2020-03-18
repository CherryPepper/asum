$(document).ready(function () {
    $(document).on('change', '.getEmployers', function () {
        let value = $(this).val();
        let result = $('select.employersResult').selectize()[0].selectize;

        if(value.length > 0){
            result.clearOptions();
            result.load(function (callback) {
                $.post('/employers/list', {role_id: value}, function (data) {
                    callback(data);
                }).fail(function () {
                    result.disable();
                    toastr.error('Ошибка при поиске сотрудников.');
                    callback();
                });
            });
        }
    });

    let added_addresses = $('.added-addresses');
    added_addresses.on('click', '.list a', function () {
        let $this = $(this);
        let parent = $this.closest('.col-sm-4');
        let next_all = parent.nextAll('.col-sm-4');
        let next = parent.next('.col-sm-4');
        let all = $('.added-addresses .col-sm-4');

        if(!parent.hasClass('disabled')) {
            next_all.find('.list').html('<ul></ul>');
            next_all.removeClass('disabled');
            all.find('input[type="checkbox"]').prop('checked', false);
            $('#selectedRegions').val('');

            $.post('/address/list', {parent_id: $this.data('id')}, function (data) {
                if (data.length) {
                    $.each(data, function (i, elem) {
                        next.find('.list ul').append('<li><label class="checkbox f-l"><input type="checkbox" class="default" data-id="'+elem.value+'"><span class="checkbox-placeholder"></span> </label> <a href="#" data-id="' + elem.value + '"> <span>' + elem.text + '</span> </a> </li>');
                    });
                } else {
                    next.find('.list ul').append('<li><p class="text-center">Не найдено ни одной записи</p></li>')
                }

            }).fail(function () {
                toastr.error('Ошибка при поиске адреса.');
            });
        }
    });

    added_addresses.on('change', 'input[type="checkbox"]', function () {
        let $this = $(this);
        let parent = $this.closest('.col-sm-4');
        let next_all = parent.nextAll('.col-sm-4');
        let selected = '';

        if(!parent.hasClass('disabled')){
            next_all.find('input[type="checkbox"]').prop('checked', false);
            next_all.addClass('disabled');
        }else
            $this.prop('checked', false);

        let all_checked = $('.added-addresses .col-sm-4 input[type="checkbox"]:checked');
        $.each(all_checked, function (i, elem) {
            selected += $(elem).data('id')+',';
        });

        $('#selectedRegions').val(selected.slice(0,-1));
    });
});