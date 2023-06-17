window.OrderReturnForm = () => {
    let shippingInformationChanged = false;

    function setFirstStep() {
        $('.next-link').attr('disabled', true).removeClass('d-none').text('Complete');
        $('.action-section').removeClass('justify-content-end').addClass('justify-content-between');
        $('.own-label-check, .returned-input, .reason-editor').removeClass('d-none');
        $('.returned-text').removeClass('d-flex').addClass('d-none');
    }

    function setCompleteStep() {
        $('.next-link').addClass('final-complete-link').removeClass('complete-link');
        $('.complete-step').removeClass('d-none');
        $('.action-section').removeClass('justify-content-between').addClass('justify-content-end');
        $('.returned-input input[type=number]').prop('readonly', true);
        $('.reason-editor, .own-label-check').addClass('d-none');
        let findChecked = $('input[name="own_label"]').is(":checked");
        if (findChecked) {
            $('#shipping_method_container').addClass('d-none');
        }
        $('.order-item-quantity').each(function(index, elem) {
            let number = parseInt($(elem).val());
            if (number == 0) {
                $(this).parents('tr').remove();
            }
        });
    }

    function setFinalCompleteStep() {
        let _form = $('.return-order-form');
        $('.final-complete-link').attr('disabled', true);

        let form = _form[0];
        let formData = new FormData(form);

        // Get shipping information for order return
        if (shippingInformationChanged) {
            let data = $('#shippingInformationEdit input, #shippingInformationEdit select').serializeArray()
            $.each(data, function (key, el) {
                formData.append(el.name, el.value);
            });
        }

        $.ajax({
            type: 'POST',
            url: _form.attr('action'),
            enctype: 'multipart/form-data',
            headers: {'X-CSRF-TOKEN': formData.get('_token')},
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                if(data.success) {
                    $('#order-return-modal').modal('hide')
                    toastr.success(data.message)
                }
            },
            error: function (messages) {
                $('.final-complete-link').attr('disabled', false);
                if (typeof messages.responseJSON.errors !== 'undefined') {
                    $.each(messages.responseJSON.errors, function (key, value) {
                        toastr.error(value)
                    });
                }
                app.alert('Processing error', 'Can not process the return order. Please try with different shipping method.');
            }
        });
    }

    function resetSteps() {
        $('#reason').val('');
        $('.final-complete-link').attr('disabled', false);
        $('.return-items-table').find('tr').show();
        $('input[name="step"]').val(1);
        $('.order-item-quantity').val(0).prop('readonly', false);
        $('.final-complete-link').text('Next').addClass('next-link');
        $('.action-section').addClass('justify-content-end').removeClass('justify-content-between');
        $('.own-label-check, .returned-input, .reason-editor').addClass('d-none');
        $('.returned-text').addClass('d-flex').removeClass('d-none');
        $('.returned-text input').prop('checked', false);
        $('.own-label-check input').prop('checked', false);
        $('.complete-step').addClass('d-none');
        $('.next-link').removeClass('final-complete-link');
        $('#shipping_method_container').removeClass('d-none');
        shippingInformationChanged = false;
    }

    $(document).ready(function () {
        $(document).find('select:not(.custom-select)').select2();

        setFirstStep()

        $('.order-item-quantity').on('change keyup', function(){
            let quantity = parseInt($(this).val());

            if (quantity !== 0) {
                $('.next-link').attr('disabled', false).text('Complete').addClass('complete-link');
            } else {
                $('.next-link').attr('disabled', true);
            }
        });

        $('.next-link').on('click', function(e) {
            e.preventDefault();
            if ($(this).hasClass('complete-link')) {
                setCompleteStep();
            } else if ($(this).hasClass('final-complete-link')) {
                if ($('.return-order-form [name="shipping_method_id"]').val() === 'dummy') {
                    app.confirm(null, 'Are you sure you want to ship using dummy label?', setFinalCompleteStep, null, null, '');
                } else {
                    setFinalCompleteStep();
                }
            }
        });

        $(document).on('hidden.bs.modal', '#shippingInformationEdit', function (e) {
            let returnOrderForm = $('.return-order-form');
            returnOrderForm.find('#cont_info_name').html($(this).find('#input-shipping_contact_information\\[name\\]').val());
            returnOrderForm.find('#cont_info_company_name').html($(this).find('#input-shipping_contact_information\\[company_name\\]').val());
            returnOrderForm.find('#cont_info_company_number').html($(this).find('#input-shipping_contact_information\\[company_number\\]').val());
            returnOrderForm.find('#cont_info_address').html($(this).find('#input-shipping_contact_information\\[address\\]').val());
            returnOrderForm.find('#cont_info_address2').html($(this).find('#input-shipping_contact_information\\[address2\\]').val());
            returnOrderForm.find('#cont_info_zip').html($(this).find('#input-shipping_contact_information\\[zip\\]').val());
            returnOrderForm.find('#cont_info_city').html($(this).find('#input-shipping_contact_information\\[city\\]').val());
            returnOrderForm.find('#cont_info_email').html($(this).find('#input-shipping_contact_information\\[email\\]').val());
            returnOrderForm.find('#cont_info_phone').html($(this).find('#input-shipping_contact_information\\[phone\\]').val());
            returnOrderForm.find('#cont_info_country_name').text($(this).find('[name="shipping_contact_information[country_id]"]').select2('data')[0].text);

            shippingInformationChanged = true;
        });

        $('.billing_contact_information-country_id').val($('.billing_contact_information-country_id').data('value')).trigger('change')
        $('.shipping_contact_information-country_id').val($('.shipping_contact_information-country_id').data('value')).trigger('change')
    });

}
