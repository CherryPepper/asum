$(document).ready(function () {

    window.address_search_settings = {
        sortField: 'text',
        render: {
            option_create: function(data, escape) {
                let elem = $($(this)[0].$input[0]);

                if(elem.data('create') === 0)
                    return false;
                else
                    return '<div class="create">Добавить <strong>' + escape(data.input) + '</strong>&hellip;</div>';
            }
        },
        create: function (input, callback) {
            if(input.length > 0){
                let $this = $(this)[0];
                let elem = $($this.$input[0]);
                let prev_elem = elem.closest('.form-wrapper').prev().find('select');
                let parent_id = 0;

                if(prev_elem.attr('name') !== undefined)
                    parent_id = prev_elem.val();

                $.post('/address/add', {parent_id: parent_id, name: input}, function (data) {
                    toastr.success('Запись успешно внесена в таблицу адресов.');
                    callback(data);
                }).fail(function () {
                    toastr.error('Ошибка при добавлении адреса.');
                    callback({});
                });
            }else
                callback({});
        },
        onChange: function (value) {
            let $this = $(this)[0];
            let elem = $($this.$input[0]);
            let parent = elem.closest('.form-wrapper');
            let next_elem = parent.next().find('select');

            checkAddressesFields(elem);

            if((value.length > 0) && (next_elem.attr('name') !== undefined)){
                next_elem = next_elem.selectize()[0].selectize;

                next_elem.clearOptions();
                next_elem.load(function (callback) {
                    $.post('/address/list', {parent_id: value}, function (data) {
                        callback(data);
                    }).fail(function () {
                        next_elem.disable();
                        toastr.error('Ошибка при поиске адреса.');
                        callback();
                    });
                });
            }
        }
    };
    $('.address-search').selectize(address_search_settings);

    function checkAddressesFields(elem) {
        let $this = elem;
        let change = false;
        let next_select = undefined;

        $.each($('select.address-search'), function(i, elem){
            if(change === true){
                if($this.val() !== '')
                    next_select = $this.closest('.form-wrapper').next().find('select');

                if((next_select !== undefined) && (next_select.attr('name') === $(elem).attr('name'))){
                    $(elem).selectize()[0].selectize.setValue('');
                    $(elem).selectize()[0].selectize.enable();
                }else{
                    $(elem).selectize()[0].selectize.setValue('');
                    $(elem).selectize()[0].selectize.disable();
                }
            }

            if($this.attr('name') === $(elem).attr('name'))
                change = true;
        });
    }

    window.initAddressSearch = function () {
        $('.address-search').selectize(address_search_settings);
    };
});