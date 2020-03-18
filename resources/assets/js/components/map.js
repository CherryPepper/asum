GoogleMapsLoader.KEY = 'AIzaSyBT4RHel1UhrfKCCCKs0JkAJ_OAanK-S8k';

GoogleMapsLoader.load(function(google) {
    if($('#google-map').length === 0)
        return false;

    google.maps.event.addDomListener( window, 'load', function () {
        window.tz_map_meters = [];

        let page = $('#google-map'),
            modal = $('#modalAjax'),
            kazan = {lat: 55.79937346, lng: 49.14367154},
            tmp_meter = null,
            is_delete = null,
            is_edit = null,
            tmp_lamps = [],
            uniqueId = 0,
            color = null,
            objects_block = $('#objects'),
            active_tooltip_id = null,
            tooltip = new google.maps.InfoWindow();

        //Map init
        let map = new google.maps.Map(document.getElementById('google-map'), {
            center: kazan,
            zoom: 13,
            maxZoom: 19,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: false
        });

        // Add "New TzObject" button to map
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(cloneNode(document.getElementById('map-new-object')));

        // Create new object action
        page.on('click', '#map-new-object', function () {
            clearTopLeft();
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(cloneNode(document.getElementById('map-steps')));

            page.find('div').css({cursor: 'url("images/tozelesh/meter-ico32.png") 12 100, default'});
            setMapStep(1);
        });

        page.on('click', '#map-lamps div.lamp', function () {
            let $this = $(this);
            let img = $this.find('img').attr('src');
            let all_lamps = page.find('#map-lamps div.lamp');

            all_lamps.removeClass('active');
            $this.addClass('active');
            page.find('div').css({cursor: 'url("'+img+'") 12 100, default'});
            is_delete = null;
        });

        google.maps.event.addListener(map, 'click', function(event) {
            // Meter registration
            if(getMapStep() === 1){
                let link = $('<a></a>');

                // Add Meter ico
                tmp_meter = new google.maps.Marker({
                    position: event.latLng,
                    icon: 'images/tozelesh/meter-ico32.png',
                    map: map
                });

                modal.modal('show', link.attr({
                    href: '/meter_registration',
                    title: 'Регистрация счетчика',
                    'show.bs.modal': function () {
                        setTimeout(function () {
                            $('#modalAjax #latLng').val('{"lat": '+tmp_meter.getPosition().lat()+
                                ', "lng": '+tmp_meter.getPosition().lng()+'}')
                        }, 1000);
                    }
                }));

                return false;
            }

            //Add lamps
            if((getMapStep() === 2 && is_delete === null)
                || (is_edit === true && is_delete === null)){

                let img = page.find('#map-lamps div.lamp.active img').attr('src');
                let inputs = page.find('#map-lamps #tmp-lamps');
                let type = page.find('#map-lamps div.lamp.active').data('type');
                let current_id = uniqueId++;

                // Add Lamp ico
                let tmp_lamp = new google.maps.Marker({
                    position: event.latLng,
                    icon: img,
                    map: map
                });
                tmp_lamps[current_id] = tmp_lamp;

                google.maps.event.addListener(tmp_lamp, "click", function () {
                    if(is_delete === true){
                        tmp_lamp.setMap(null);
                        $('#marker-'+current_id).remove();
                    }
                });

                //Create input with lamp coordinates
                let json_data = {type: type, coordinates: {lat: tmp_lamp.getPosition().lat(), lng: tmp_lamp.getPosition().lng()}};
                inputs.append("<input type='hidden' id='marker-"+current_id+"' value='"+JSON.stringify(json_data)+"'>");

                return false;
            }
        });

        // Cancel creating
        page.on('click', '#map-steps #cancel', function () {
            clearTopLeft();
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(cloneNode(document.getElementById('map-new-object')));

            page.find('div').css({cursor: 'auto'});
            setMapStep(0);

            let object = $('#object-id');
            let object_id = parseInt(object.val());

            if(!isNaN(object_id)){
                object.val('');

                $.get('/cancel_creation/'+object_id);
                tmp_meter.setMap(null);

                $.each(tmp_lamps, function (i, lamp) {
                    lamp.setMap(null);
                });
            }
        });

        //Delete lamp
        page.on('click', '#map-lamps #remove', function () {
            let all_lamps = page.find('#map-lamps div.lamp');

            all_lamps.removeClass('active');
            toastr.info('Кликайте по фонарям, которые хотите убрать');
            page.find('div').css({cursor: 'pointer'});
            is_delete = true;
        });

        //Save lamps
        page.on('click', '#map-lamps #save', function () {
            let $this = $(this),
                lamps = page.find('#map-lamps #tmp-lamps input'),
                post = {};

            post.object_id = $('#object-id').val();
            post.lamps = {};
            $this.prop('disabled', true);

            $.each(lamps, function (i, elem) {
                post.lamps[i] = $(elem).val();
            });

            $.post('/save_lamps', post, function (data) {
                if(data.status === 'success'){
                    toastr.success(data.message);
                    map.controls[google.maps.ControlPosition.TOP_RIGHT].clear();
                    setMapStep(3);
                }
            }).statusCode({
                422: function (data) {
                    $('form.meter-registration label').removeClass('text-danger');
                    $('form.meter-registration div.form-group').removeClass('has-error');

                    $.each(data.responseJSON, function (i) {
                        let input;
                        let name = i.split('.');

                        if(name[1] !== undefined)
                            input = $('*[name="'+name[0]+'['+name[1]+']'+'"]');
                        else
                            input = $('*[name="'+i+'"]');

                        let parent = input.closest('.form-wrapper');

                        parent.find('label').addClass('text-danger');
                        parent.find('div.form-group').addClass('has-error');
                    });

                    $this.prop('disabled', false);
                }
            });
        });

        //Save object
        page.on('click', '#save-object', function () {
            let $this = $(this),
                url = (is_edit === true) ? '/edit_object' : '/save_object',
                lamps = page.find('#map-lamps #tmp-lamps input'),
                post = {
                    object_id: $('#object-id').val(),
                    time_on: page.find('#time-on').val(),
                    time_off: page.find('#time-off').val(),
                };

            if(is_edit === true){
                post.lamps = {};

                $.each(lamps, function (i, elem) {
                    post.lamps[i] = $(elem).val();
                });
            }

            $this.prop('disabled', true);

            $.post(url, post, function (data) {
                if(data.status === 'success'){
                    toastr.success(data.message);
                    $('#object-id').val('');

                    clearTopLeft();
                    map.controls[google.maps.ControlPosition.TOP_LEFT].push(cloneNode(document.getElementById('map-new-object')));

                    setTimeout(function () {
                        window.location = '';
                    }, 5000);
                }
            }).statusCode({
                422: function (data) {
                    $('form.meter-registration label').removeClass('text-danger');
                    $('form.meter-registration div.form-group').removeClass('has-error');

                    $.each(data.responseJSON, function (i) {
                        let input;
                        let name = i.split('.');

                        if(name[1] !== undefined)
                            input = $('*[name="'+name[0]+'['+name[1]+']'+'"]');
                        else
                            input = $('*[name="'+i+'"]');

                        let parent = input.closest('.form-wrapper');

                        parent.find('label').addClass('text-danger');
                        parent.find('div.form-group').addClass('has-error');
                    });

                    $this.prop('disabled', false);
                }
            });
        });

        // edit meter
        page.on('click', '#edit-meter', function () {
            let modal = $('#modalAjax');
            let $this = $(this);

            modal.modal('show', $this);

            return false;
        });

        modal.on('hide.bs.modal', function () {
            if(getMapStep() === 1)
                tmp_meter.setMap(null);
        });

        // Edit object
        page.on('click', '#edit-object', function () {
            clearTopLeft();
            tooltip.close();
            is_edit = true;

            let block = $('#map-lamps').clone(),
                set_time_block = $('.hidden #map-steps #set-time').clone().removeClass('hidden').removeClass('mt-20'),
                inputs = block.find('#tmp-lamps'),
                object = $('.hidden #objects .meters[data-id="'+active_tooltip_id+'"]');

            // Set time on/off for edit
            set_time_block.find('#time-on').val(object.attr('data-time_on'));
            set_time_block.find('#time-off').val(object.attr('data-time_off'));
            block.find('a#cancel').removeClass('hidden');
            block.find('#save').attr('id', 'save-object');

            $('#object-id').val(active_tooltip_id);

            // Each lamps in object
            $.each(tz_map_meters[active_tooltip_id]['lamps'], function (i, lamp) {
                let icon = lamp.getIcon().split('/'),
                    current_id = uniqueId++;

                // Set default icon
                icon[2] = 'black';
                icon = icon.join('/');
                lamp.setIcon(icon);
                lamp.setAnimation(null);


                // Add event on delete marker
                google.maps.event.addListener(lamp, "click", function () {
                    if(is_delete === true){
                        lamp.setMap(null);
                        $('#marker-'+current_id).remove();
                    }
                });

                //Create input with lamp coordinates
                let json_data = {type: lamp.lamp_type, coordinates: {lat: lamp.getPosition().lat(), lng: lamp.getPosition().lng()}};
                inputs.append("<input type='hidden' id='marker-"+current_id+"' value='"+JSON.stringify(json_data)+"'>");
            });

            // Set style for block....
            set_time_block.css({
                width: '238px', background: '#fff', border: '1px solid #dce2e9', margin: 0, 'padding-top': '10px'
            });
            set_time_block.find('.row').css({
                margin: '5px 0 0 0 !important'
            });

            // Push block
            block.find('.buttons').before(set_time_block);
            map.controls[google.maps.ControlPosition.TOP_RIGHT].push(block.get(0));

            // Init datetime picker
            page.find('#time-on, #time-off').datetimepicker({
                locale: 'ru',
                format: 'HH:mm'
            });

            page.find('#map-lamps div.lamp:first').trigger('click');

            return false;
        });

        function clearTopLeft() {
            map.controls[google.maps.ControlPosition.TOP_LEFT].clear();
            map.controls[google.maps.ControlPosition.TOP_RIGHT].clear();
        }

        function cloneNode(node) {
            return node.cloneNode(true);
        }

        window.getMapStep = function () {
            return parseInt($('#current-step').val());
        };
        window.setMapStep = function (step) {
            let blocks = $('#google-map #map-steps > div');
            blocks.removeClass('active').removeClass('completed');

            if(step === 2){
                map.controls[google.maps.ControlPosition.TOP_RIGHT].push(cloneNode(document.getElementById('map-lamps')));
                page.find('#map-lamps div.lamp:first').trigger('click');
            }
            else if(step === 3){
                page.find('#time-on, #time-off').datetimepicker({
                    locale: 'ru',
                    format: 'HH:mm'
                });

                page.find('#save-object').removeClass('hidden');
                page.find('#set-time').removeClass('hidden');
                page.find('div').css({cursor: 'auto'});
            }

            $.each(blocks, function (i, elem) {
                let block_step = i+1;

                if(block_step < step)
                    $(elem).addClass('completed');
                else if(block_step === step)
                    $(elem).addClass('active');
            });

            $('#current-step').val(step);
        };


        //Show added object
        $.each(objects_block.find('.meters'), function (i, object) {
            object = $(object);
            let lamps_arr = [];

            //Add meter ico
            let meter = new google.maps.Marker({
                position: object.data('coordinates'),
                icon: 'images/tozelesh/meter-ico32.png',
                map: map
            });

            meter.addListener('click', function() {
                if(is_edit === true)
                    return false;

                map.setZoom(18);
                map.setCenter(meter.getPosition());

                tooltip.setContent(prepareTooltip(object));
                tooltip.open(map, meter);

                animateLamps(object.attr('data-id'));

                active_tooltip_id = parseInt(object.attr('data-id'));
            });

            tz_map_meters[object.attr('data-id')] = meter;

            //Colors for lamps
            switch (parseInt(object.attr('data-status'))){
                case 0:{
                    color = 'red';
                    break;
                }
                case 1:{
                    color = 'green';
                    break;
                }
                case 2:{
                    color = 'yellow';
                    break;
                }
            }

            //Add lamps ico
            $.each(objects_block.find('.lamps-'+object.attr('data-id')), function (i, lamp) {
                lamp = $(lamp);

                let lamp_marker = new google.maps.Marker({
                    position: lamp.data('coordinates'),
                    icon: 'images/tozelesh/'+color+'/'+lamp.data('img'),
                    map: map,
                    lamp_type: lamp.attr('data-type')
                });

                lamps_arr.push(lamp_marker);

                lamp_marker.addListener('click', function() {
                    new google.maps.event.trigger( meter, 'click' );
                });
            });

            tz_map_meters[object.attr('data-id')]['lamps'] = lamps_arr;
        });

        // Animate lamps when tooltip open
        function animateLamps(object_id) {
            if(active_tooltip_id !== null){
                $.each(tz_map_meters[active_tooltip_id]['lamps'], function (i, lamp) {
                    lamp.setAnimation(null);
                });
            }

            $.each(tz_map_meters[object_id]['lamps'], function (i, lamp) {
                lamp.setAnimation(google.maps.Animation.BOUNCE);
            });
        }

        // Close tooltip event
        google.maps.event.addListener(tooltip, 'closeclick',function(){
            $.each(tz_map_meters[active_tooltip_id]['lamps'], function (i, lamp) {
                lamp.setAnimation(null);
            });
        });

        // Prepare tooltip
        function prepareTooltip(object) {
            let tooltip_info = $('#tooltip-info'),
                status = {},
                lamps = {cnt: 0, consumption: 0},
                time_on_off = 'Не указано';

            // Each lamps in object
            $.each(objects_block.find('.lamps-'+object.attr('data-id')), function (i, lamp) {
                lamps.cnt++;
                lamps.consumption += parseFloat($(lamp).data('consumption'));
            });

            //TzObject id
            tooltip_info.find('#object-id').val(object.attr('data-id'));

            //TzObject status
            switch (parseInt(object.attr('data-status'))){
                case 0:{
                    status = {cur:'Выключен', set:'Включить', cls: 'off', act: 1};

                    break;
                }
                case 1:{
                    status = {cur:'Включен', set:'Выключить', cls: 'on', act: 0};

                    break;
                }
                case 2:{
                    status = {cur:'Не отвечает', set:'Включить', cls: 'not-response', act: 1};

                    break;
                }
            }

            tooltip_info.find('.status').attr('class', 'status '+status.cls).text(status.cur);
            tooltip_info.find('#on-off a').attr('href', '/meter_instruction/set/'+object.attr('data-meter')+'/'+status.act).text(status.set);

            //Last value
            tooltip_info.find('#last-value').text(object.attr('data-value'));
            tooltip_info.find('#refresh-meter-value #refresh-btn').attr('href', '/meter_instruction/refresh/'+object.attr('data-meter'));
            tooltip_info.find('#edit-meter').attr('href', '/meters/edit/'+object.attr('data-meter'));

            //Address
            tooltip_info.find('#address').text(object.attr('data-address'));

            //Lamps count
            tooltip_info.find('#lamps-cnt').text(lamps.cnt);

            //Lamps consumption
            tooltip_info.find('#lamps-consumption').text(parseFloat(lamps.consumption).toFixed(2));
            tooltip_info.find('#consumption-report').attr('href', '/report/'+object.attr('data-meter'));

            // Time on/off
            if((object.attr('data-time_on') !== '') && (object.attr('data-time_off') !== ''))
                time_on_off = object.attr('data-time_on')+' / '+object.attr('data-time_off');
            tooltip_info.find('#time-on-off').text(time_on_off);

            //Delete object
            tooltip_info.find('#delete-object').attr('data-id', object.attr('data-id')).attr('data-url', '/delete_object');

            return tooltip_info.html();
        }
    });
});
