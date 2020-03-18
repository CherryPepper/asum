$(document).ready(function () {
    let page = $('#google-map');

    $(document).on('click', '#set-meter-status, #refresh-meter-value #refresh-btn', function () {
        let $this = $(this),
            url = $this.attr('href');

        if(url !== '#'){
            $this.attr('href', '#');

            console.log($this.attr('id'));

            if($this.attr('id') === 'set-meter-status')
                $this.append(' <i class="fa fa-refresh fa-spin"></i>');
            if($this.attr('id') === 'refresh-btn')
                $this.find('.fa-refresh').addClass('fa-spin');

            $.get(url, function (data) {
                if(data.status === 'success'){
                    toastr.success(data.message);
                    checkInstructionProgress(data.id)
                }
            });
        }
        return false;
    });

    let checkInstructionProgress = function (id) {
        let interval = setInterval(function () {
            $.get('/meter_instruction/check/'+id, function (data) {
                if(data.status !== undefined){
                    clearInterval(interval);

                    let map_page = $('#google-map'),
                        object_id = parseInt(map_page.find('#object-id').val());

                    // Set Meter Status Instruction
                    if(data.setStatus !== undefined){
                        let block = page.find('#change-meter-status'),
                            link = block.find('#set-meter-status'),
                            cur_status = null,
                            status = {
                                0: {st: 0, title: 'Выключен', action: 'Включить', cls: 'status off', set: 1, color: 'red'},
                                1: {st: 1, title: 'Включен', action: 'Выключить', cls: 'status on', set: 0, color: 'green'},
                                2: {st: 2, title: 'Не отвечает', action: 'Включить', cls: 'status not-response', set: 1, color: 'yellow'}
                            };

                        link.find('.fa-refresh').remove();

                        if(data.status === 'success'){
                            cur_status = status[data.setStatus.status];
                            toastr.success(data.message);
                        }
                        else if(data.status === 'error'){
                            cur_status = status[2];

                            toastr.error(data.message);
                        }

                        block.find('.status').attr('class', cur_status.cls).text(cur_status.title);
                        link.attr('href', '/meter_instruction/set/'+data.setStatus.meter_id+'/'+cur_status.set)
                            .text(cur_status.action);

                        if(map_page.length > 0){
                            $('.hidden #objects .meters[data-id="'+object_id+'"]').attr('data-status', cur_status.st);

                            $.each(tz_map_meters[object_id].lamps, function (i, lamp) {
                                let icon = lamp.getIcon().split('/');
                                icon[2] = cur_status.color;
                                icon = icon.join('/');

                                lamp.setIcon(icon);
                            });
                        }
                    }

                    //Update value Instruction
                    if(data.newVal !== undefined){
                        let block = $('#refresh-meter-value'),
                            link = block.find('#refresh-btn');

                        if(data.status === 'success'){
                            toastr.success(data.message);

                            block.find('#last-value').text(data.newVal.value);

                            if(map_page.length > 0)
                                $('.hidden #objects .meters[data-id="'+object_id+'"]').attr('data-value', data.newVal.value);
                        }
                        else if(data.status === 'error')
                            toastr.error(data.message);

                        link.find('.fa-refresh').removeClass('fa-spin');
                        link.attr('href', '/meter_instruction/refresh/'+data.newVal.meter_id);
                    }
                }
            }).fail(function () {
                clearInterval(interval);
            });
        }, 1500)
    };
});