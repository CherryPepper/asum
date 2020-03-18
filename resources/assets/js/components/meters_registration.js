$(document).ready(function () {
    $(document).on('click', '.meters-registration .send-ajax-form, ' +
        '.add-control .send-ajax-form', function () {
        let $this = $(this);
        let form = $this.closest('form');
        let progress_bar = form.find('.meter-registration-progress');
        let tz_completed = false;

        $this.prop('disabled', true).append('<i class="fa fa-refresh fa-spin"></i>');
        $.post(form.attr('action'), form.serialize(), function (data) {
            $('form.meter-registration label').removeClass('text-danger');
            $('form.meter-registration div.form-group').removeClass('has-error');

            if(data.status === 'success'){
                toastr.success(data.message);
                progress_bar.show();

                /** Check registration interval progress */
                let progress = setInterval(function () {
                    $.get('/meters/registration_progress/'+data.id, function (data) {
                        if(data.type === 'toast'){
                            if(tz_completed === true)
                                return false;

                            if(data.status === 'success'){
                                toastr.success(data.message);

                                progress_bar.find('.progress-bar').css('width', '100%');
                                progress_bar.find('span').text('100%');

                                if('tozelesh' in data){
                                    tz_completed = true;

                                    setMapStep(2);
                                    $('#object-id').val(data.object_id);
                                    $('#modalAjax').modal('toggle');

                                    clearInterval(progress);
                                }else
                                    setTimeout(function () {
                                        window.location = '/meters/registration';
                                    }, 5000);
                            }
                            else{
                                toastr.error(data.message);
                                $this.prop('disabled', false);
                                progress_bar.hide();
                                progress_bar.find('.progress-bar').css('width', '5%');
                                progress_bar.find('span').text('5%');
                            }

                            $this.find('i').remove();
                            clearInterval(progress);
                        }else{
                            if(data.completed > 0){
                                let percent = (data.completed/data.all)*100;

                                progress_bar.find('.progress-bar').css('width', percent+'%');
                                progress_bar.find('span').text(percent+'%');
                            }
                        }
                    });
                }, 1000);
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
                $this.find('i').remove();
            }
        });
        return false;
    });
});