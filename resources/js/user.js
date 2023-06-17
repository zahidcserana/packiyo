window.User = function () {

    $(document).ready(function () {
        $(document).find('select:not(.custom-select)').select2();
    });

    window.datatables.push({
        selector: '#users-table',
        resource: 'users',
        ajax: {
            url: '/user/data-table/'
        },
        columns: [
            {
                'non_hiddable': true,
                "orderable": false,
                "class": "text-left",
                "title": "",
                "name": "users.id",
                "data": function (data) {
                    return `<a type="button" class="table-icon-button" href="${data["link_edit"]}">
                            <i class="picon-edit-filled icon-lg" title="Edit"></i>
                        </a>`;
                }
            },
            {"title": "Email", "data": "email", "name": "contact_informations.email"},
            {"title": "Name", "data": "name", "name": "contact_informations.name"},
            {"title": "Status", "data": "status", "name": "status"},
            {"title": "Pin", "data": "pin", "name": "pin"},
            {
                'non_hiddable': true,
                "orderable": false,
                "class": "text-left",
                "title": "",
                "name": "users.id",
                "data": function (data) {
                    return app.tableDeleteButton(
                        `Are you sure you want to delete ${data.name}?`,
                        data.link_delete
                    );
                }
            },
        ],
    })

    $(document).ready(function() {
        $(document).find('select:not(.custom-select)').select2();
    })
}

$(document).ready(() => {
    $(document).on('click', '.userForm .saveButton,.userContainer .globalSave', () => {
        $(this).addClass('d-none')

        let form = $('form.userForm')
        let formData = new FormData(form.get(0))

        form.removeClass('editable')
        form.find('.loading').removeClass('d-none')

        $.ajax({
            type: 'POST',
            url: form.data('action'),
            enctype: 'multipart/form-data',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {

                if( data.redirect_url !== undefined ){
                    window.location.href = data.redirect_url;
                }
                else{
                    form.find('.loading').addClass('d-none')
                    form.find('.saveSuccess').removeClass('d-none').css('display', 'block').fadeOut(5000)
                    if (form.find('input[type="password"]').val()) {
                        toastr.success('Password updated successfully!')
                        form.find('input[type="password"]').val('')
                    }
                    toastr.success(data.message)
                    clearValidationMessages(form)
                }
            },
            error: function (response) {
                form.find('.loading').addClass('d-none')
                form.find('.saveError').removeClass('d-none').fadeOut(5000)
                form.addClass('editable')

                appendValidationMessages(form, response)
            },
        })
    })
})
