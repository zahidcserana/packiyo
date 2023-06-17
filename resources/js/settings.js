window.Settings = function() {

    $(document).on('click', '.editFormContent', function() {
        $('.formsContainer').find('form.editable').removeClass('editable')

        let form = $(this).closest('form');

        form.addClass('editable')
        form.find('.saveSuccess').addClass('d-none').css('display', 'none')
        form.find('.saveError').addClass('d-none').css('display', 'none')
    })

    $(document).on('click', '.saveButton', function() {
        $(this).addClass('d-none')
        $(document).find('.form-error-messages').remove()

        let _form = $(this).closest('.settingsForm');

        window.updateCkEditorElements()

        _form.removeClass('editable')
        _form.find('.loading').removeClass('d-none')

        let form = _form[0];
        let formData = new FormData(form);

        $.ajax({
            type: 'POST',
            url: _form.attr('action'),
            enctype: 'multipart/form-data',
            headers: {'X-CSRF-TOKEN': formData.get('_token')},
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                _form.find('.loading').addClass('d-none')
                _form.find('.saveSuccess').removeClass('d-none').css('display', 'block').fadeOut(5000)
                toastr.success(data.message)
            },
            error: function (messages) {
                _form.find('.loading').addClass('d-none')
                _form.find('.saveError').removeClass('d-none').removeClass('d-none').css('display', 'block').fadeOut(5000)
                _form.addClass('editable')

                $.map(messages.responseJSON.errors, function (value, key) {
                    let label = _form.find('label[data-id="' + key + '"]')
                        .append('<span class="validate-error text-danger form-error-messages">&nbsp;&nbsp;&nbsp;&nbsp;' + value[0] + '</span>')

                    let error_type = key.split('.')

                    if (error_type && error_type.length && error_type[0] === 'kit_items') {
                        $(document).find('.validation_errors').append('<span class="validate-error text-danger form-error-messages">' + value[0] + '</span><br>')
                    }

                    let hasError = label.closest('.tab-pane').attr('id');
                    $(document).find('a[href="#' + hasError + '"]').addClass('text-danger')
                })

                let hasErrorTab = $(document).find('.validate-error').closest('.tab-pane').attr('id');

                $(document).find('a[href="#' + hasErrorTab + '"]').first().trigger('click')
            }
        });
    })

    $(document).on('click', '.globalSave', function (e) {
        e.preventDefault();
        e.stopPropagation();

        $(document).find('.form-error-messages').remove()
        let _form = $(this).closest('#globalForm');

        let formData = new FormData();

        const forms = _form.find('form');

        window.updateCkEditorElements()

        $.each(forms, function (index, form) {
            let data = $(form).serializeArray()
            $.each(data, function (key, el) {
                formData.append(el.name, el.value);
            })
        })

        $.ajax({
            type: 'POST',
            url: _form.data('form-action'),
            enctype: 'multipart/form-data',
            headers: {'X-CSRF-TOKEN': formData.get('_token')},
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                $('.smallForm').removeClass('editable');
                _form.find('input[type="checkbox"]').each(function() {
                    let spanText = ''
                    if(this.checked) {
                        spanText = 'Yes'
                    }
                    $(this).closest('.editCheckbox').find('.checkbox-result').html(spanText)
                })
                toastr.success(data.message)
                $("html, body").animate({ scrollTop: 0 }, "slow");
            },
            error: function (messages) {
                if (messages.responseJSON.errors) {
                    $.each(messages.responseJSON.errors, function (key, value) {
                        toastr.error(value)
                    });
                }
                $(document).find('.validate-error').eq(0).closest('form').addClass('editable')
                $("html, body").animate({ scrollTop: 0 }, "slow");
            }
        })
    })

};
