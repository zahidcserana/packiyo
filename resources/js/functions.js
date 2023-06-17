window.checkDeleteButton = function (){
        $('.order-item-fields:not(.order-item-deleted)').length === 1 ? $('.delete-item').prop('disabled', true) : $('.delete-item').prop('disabled', false);
    };

window.hideItems = function () {
    if(!$('select[name="order_id"]').val()) {

        $('#item_container, #add_item').hide();
    }
};

window.dateTimePicker = function () {
    $('.datetimepicker').daterangepicker({
        autoUpdateInput: false,
        singleDatePicker: true,
        timePicker: false,
        timePicker24Hour: false,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    $('.datetimepicker').on('hide.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD')).trigger('change');
    });

    $('.datetimepicker').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
};

window.dtDateRangePicker = function () {
    $('.dt-daterangepicker').daterangepicker({
        autoUpdateInput: false,
        singleDatePicker: true,
        timePicker: false,
        timePicker24Hour: false,
        locale: {
            cancelLabel: 'Clear',
            format: 'Y-MM-DD'
        }
    });

    $('.dt-daterangepicker').on('hide.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD')).trigger('change');
    });

    $('.dt-daterangepicker').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
};

window.deleteItemFromTableButton = function () {
    $(document).on('click', '.delete-item', function (event) {
        $(this).parent().parent().find('.reset_on_delete').val(0);
        $(this).parent().parent().hide().addClass('order-item-deleted');
        checkDeleteButton();
        event.preventDefault();
    });
}

window.appendValidationMessages = function (modal, response) {
    let errors = response.responseJSON.errors

    clearValidationMessages(modal)

    // Append validation message to each input label
    for (const [name, message] of Object.entries(errors)) {
        let htmlName = name

        if (name.includes('.')) {
            let nameArray = name.split('.')
            htmlName = nameArray[0]

            for (let i = 1; i < nameArray.length; i++) {
                htmlName = htmlName + '[' + nameArray[i] + ']'
            }
        }

        let input = modal.find('[name="' + htmlName + '"]')

        if (input.length === 0) {
            input = modal.find('[name^="' + htmlName + '"]')
        }

        let label = input.parents('.form-group').find('label')

        if (label.length === 0) {
            label = input.parents('.form-group')
        }


        label.append(`
            <span class="validate-error text-danger form-error-messages">
                &nbsp;&nbsp;&nbsp;&nbsp;
                ${message}
            </span>
        `)

        toastr.error(message)
    }

    // Highlight tabs with validation errors
    modal.find('.nav-item > a').each(function () {
        let contentId = $(this).attr('href')

        if ($(contentId).find('.validate-error').length) {
            $(this).addClass('text-danger')
        }
    })

    // Click on first tab with validation error
    modal.find('a.text-danger').first().trigger('click')
}

window.clearValidationMessages = function (modal) {
    modal.find('span.validate-error').remove()

    modal.find('.nav-item > a.text-danger').removeClass('text-danger')
}

window.resetModalWithForm = function (modal) {
    modal.find('form')[0].reset()

    modal.find('select').val(null).trigger('change')

    modal.find('.nav-item:first-child > a').tab('show')

    clearValidationMessages(modal)

    modal.modal('hide')
}

window.isNumberic = function (value) {
    return /^-?\d+$/.test(value);
}

window.serializeFilterForm = function (filterForm) {
    let request = {}

    filterForm
        .serializeArray()
        .map(function(input) {
                const value = input.value
                const isArray = input.name.includes('[]')
                const name = input.name.replace('[]', '')

                if (isArray) {
                    if (typeof request[name] === 'undefined') {
                        request[name] = []
                    }
                    request[name].push(value)
                } else {
                    request[name] = value
                }
            }
        );

    return request
}

window.queryUrl = function (params) {
    query = $.isEmptyObject(params) ? '' : '?' + $.param(params);

    return history.pushState({},'', location.protocol + '//' + location.host + location.pathname  + query);
}

window.loadFilterFromQuery = function (filterForm) {
    const searchParams = new URLSearchParams(document.location.search)

    searchParams.forEach((value, key) => {
        const element = filterForm.find(`[name="${key}"]`)

        if (key.includes('[]')) {
            if (element.length && element[0].nodeName == 'SELECT') {
                let option = $(element).find(`option[value="${value}"]`)
                if (option.length) {
                    option.prop('selected', true)
                } else {
                    option = new Option(value, value, true, true)
                    element.append(option).trigger('change')
                }
            }
        } else {
            element.val(value)
        }
    })
}

window.auditLog = function () {
    if ($.fn.dataTable.isDataTable('#audit-log')) {
        $('#audit-log').DataTable({
            destroy: true,
            sDom: 'l<"row view-filter"<"col-sm-12"<"clearfix">>>t<"row view-pager"<"col-sm-12"<"text-center"ip>>>',
            pagingType: 'simple',
            language: window.datatableGlobalLanguage,
            order: [0, 'desc'],
            lengthMenu: [[10,25,50,100], [10,25,50,100]],
            lengthChange: true,
            info: false,
            bInfo : false,
        });
    }
}

window.reloadAuditLog = function () {
    $.ajax({
        type:'GET',
        serverSide: true,
        url: $('#audit-log-container').attr('audit-url'),
        success:function(data) {
            $('#audit-log-data').html(data);
        },
    });
}
