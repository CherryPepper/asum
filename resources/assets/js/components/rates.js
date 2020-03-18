$(document).ready(function () {
    let page = $('.rate-create');
    let first_point_block = $('#firstPointBlock');
    let points_nav_block = $('#pointsNavBlock');
    let radius = 360;
    let hours = 24;
    let colors = {
        1: '#0ec8a2', 2: '#f95858', 3: '#d0e92d', 4: '#9d2de9',
        5: '#e99a2d', 6: '#e92db3', 7: '#005db4', 8: '#31ae00',
        9: '#eae300', 10: '#78c7f0', 11: '#89ff65', 12: '#8e32fb',
        13: '#f8fb32', 14: '#fb7932', 15: '#005480', 16: '#ce5eb2',
        17: '#589ba1', 18: '#5c3285', 19: '#498532', 20: '#d68c5b',
        21: '#d65bb7', 22: '#838383', 23: '#9900bd', 24: '#008940'
    };

    page.on('change', 'input[name="type"]', function () {
        let $this = $(this);
        let knob_tmp = $('#template-knobs').clone();
        let inputs_tmp = $('#template-intervals-inputs').clone();
        let knobs = page.find('.intervals .knobs');
        let inputs = page.find('.intervals-inputs');
        let multiple_buttons = page.find('.buttons');

        knobs.html('');
        inputs.html('');
        knob_tmp.find('input').addClass('knob');
        first_point_block.show();
        points_nav_block.hide();
        multiple_buttons.hide();

        if(parseInt($this.val()) === 1){
            knobs.html(knob_tmp.html());
            inputs.html(inputs_tmp.html());

            $('.knob').knob({});
            page.find('button.send-form').prop('disabled', false);
        }else{
            multiple_buttons.show();
            knob_tmp.find('input').attr('class', 'knob-first').attr('data-cursor', 10).prop('readonly', false);
            knobs.html(knob_tmp.html());
            $('#createNewPoint').removeClass('btn-default').addClass('btn-info').attr('disabled', false);

            $('.knob-first').knob({
                release : function (v) { this.$.attr('value', parseInt(v)); }
            });
            page.find('button.send-form').prop('disabled', true);
        }
    });

    page.on('click', '#appleFirstPoint', function () {
        let knob_tmp = $('#template-knobs').clone();
        let knobs = page.find('.intervals .knobs');
        let first_point = knobs.find('.knob-first');
        let new_interval = knob_tmp.find('input').attr('class', 'knob').prop('readonly', false)
            .attr('value', 1);

        if(first_point.val() > 12)
            new_interval.attr('data-angleOffset', (first_point.val()-hours)*15);
        else
            new_interval.attr('data-angleOffset', first_point.val()*15);

        knobs.html(new_interval);
        $('.knob').knob({
            release : function (v) { this.$.attr('value', parseInt(v)); }
        });

        toastr.success('Начальная точка установлена на '+ getTime(first_point.val()));
        first_point_block.hide();
        points_nav_block.show();
    });

    page.on('click', '#clearIntervals', function () {
        let knob_tmp = $('#template-knobs').clone();
        let knobs = page.find('.intervals .knobs');
        let inputs = page.find('.intervals-inputs');

        inputs.html('');
        first_point_block.show();
        points_nav_block.hide();
        $('#createNewPoint').removeClass('btn-default').addClass('btn-info').attr('disabled', false);
        page.find('button.send-form').prop('disabled', true);

        knob_tmp.find('input').attr('class', 'knob-first').attr('data-cursor', 10).prop('readonly', false);
        knobs.html(knob_tmp.html());

        $('.knob-first').knob({
            release : function (v) { this.$.attr('value', parseInt(v)); }
        });
    });

    page.on('click', '#createNewPoint', function () {
        if($(this).attr('disabled') === 'disabled') return false;

        let knob_tmp = $('#template-knobs').clone();
        let knobs = page.find('.intervals .knobs');
        let last_knob = knobs.find('input:last');
        let cnt_points = knobs.find('input').length;
        let prev_offset = parseInt(last_knob.attr('data-angleOffset'));
        let offset = prev_offset+(last_knob.val()*15);
        let total_val = 0;
        let angle;

        $.each(knobs.find('input'), function (i, e) {
            total_val += parseInt($(e).val());
        });
        angle = total_val*15;

        if(total_val >= hours){
            $(this).removeClass('btn-info').addClass('btn-default').attr('disabled', true);
            knobs.html(knobs.find('input').prop('readonly', true));

            $('.knob').knob();
            toastr.error('Временная шкала уже заполнена');
        }else{
            knob_tmp.find('input').attr('class', 'knob').prop('readonly', false)
                .attr('data-fgColor', colors[cnt_points]).attr('value', 1).attr('data-angleOffset', offset)
                .attr('data-max', hours-total_val).attr('data-angleArc', radius-angle);

            knobs.html(knobs.find('input'));
            knobs.append(knob_tmp.html());

            $('.knob').knob({
                release : function (v) { this.$.attr('value', parseInt(v)); }
            });
        }
    });

    page.on('click', '#appleIntervals', function () {
        let knobs = page.find('.intervals .knobs');
        let inputs_tmp = $('#template-intervals-inputs').clone();
        let inputs = page.find('.intervals-inputs');
        let total_val = 0;
        let input;

        $.each(knobs.find('input'), function (i, e) {
            total_val += parseInt($(e).val());
        });

        if(total_val >= 24){
            inputs.html('');
            knobs.html(knobs.find('input').prop('readonly', true));
            $('.knob').knob();

            $.each(knobs.find('input'), function (i, e) {
                let offset = $(e).attr('data-angleoffset')/15;
                let end = offset+parseInt($(e).attr('value'));

                if(offset < 0) offset = hours+offset;
                if(end < 0) end = hours+end;
                if(end > 24) end = end-hours;

                let time_start = getTime(offset);
                let time_end = getTime(end);

                inputs_tmp.find('.form-wrapper').css('background-color', $(e).attr('data-fgColor'));
                inputs_tmp.find('input[name="time_start[]"]').attr('value', time_start);
                inputs_tmp.find('input[name="time_end[]"]').attr('value', time_end);
                inputs.append(inputs_tmp.html());
            });

            page.find('button.send-form').prop('disabled', false);
        }else
            toastr.error('Прежде чем продолжить, необходимо заполнить шкалу интервалов');


    });

    page.on('click', 'button.send-form', function () {
        let inputs = page.find('.intervals-inputs');
        let flag_error = 0;

        $.each(inputs.find('input[name="price[]"]'), function (i, e) {
            if(parseFloat($(e).val()) <= 0){
                $(e).closest('div').addClass('has-error');
                flag_error = 1;
            }
        });

        if(flag_error === 1){
            toastr.error('Необходимо указать цену для каждого интервала');
            return false;
        }
    });

    $('.knob').knob({});

    function getTime(v) {
        if(parseInt(v) < 10)
            v = '0'+v;
        return v+':00';
    }
});