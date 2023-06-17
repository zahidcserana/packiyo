window.ReturnStatus = function () {

    $(document).ready(function () {
        $(document).find('select:not(.custom-select)').select2();
    });

    window.datatables.push({
        selector: '#return-status-table',
        resource: 'return-status',
        ajax: {
            url: '/return_status/data-table'
        },
        columns: [
            {
                "non_hiddable":true,
                "orderable": false,
                "class": "text-left",
                "title": "",
                "name":"id",
                "data": function (data) {
                    let editButton = `
                        <a type="button" title="Edit" href="${data.link_edit}">
                            <i class="picon-edit-filled icon-lg" title="Edit"></i>
                        </a>`;

                    return editButton
                },
            },
            {
                "title": "Name",
                "data": "name",
                "name": "return_statuses.name",
            },
            {
                "title": "Customer",
                "data": function (data) {
                    return '<a href="'+ data.customer.url +'" class="text-neutral-text-gray">' + data.customer.name + '</a>';
                },
                "name": "customer_contact_information.name",
                "class": "text-neutral-text-gray"
            },
            {
                "title": "Color",
                "data": function (data) {
                    if(data.color === null) {
                        return '<div class="d-flex align-items-center">Transparent</div>';
                    } else {
                        return '<div class="d-flex align-items-center"><div class="color-preview" style="background-color: ' + data.color + '"></div><span class="ml-2">' + data.color + '</span></div>';
                    }
                },
                "name": "color",
                "class": "text-neutral-text-gray"
            },
            {
                "title": "Created at",
                "data": "created_at",
                "name": "created_at",
                "visible": false
            },
            {
                'non_hiddable': true,
                "orderable": false,
                "class":"text-right",
                "title": "",
                "name":"action",
                "data": function (data) {
                    return app.tableDeleteButton(
                        `Are you sure you want to delete ${data.name}?`,
                        data.link_delete
                    );
                }
            }
        ],
        dropdownAutoWidth : true,
    })

    $(document).ready(function() {
        $(document).on('show.bs.modal', '#create-edit-modal', function (e) {
            let modal = $(this)
            let button = $(e.relatedTarget)

            $.ajax({
                url: button.attr('href'),
                success: function (data) {
                    modal.find('div').html(data)
                    Coloris({
                        el: '.coloris',
                        swatches: [
                            '#F7860B',
                            '#e9c46a',
                            '#FF6A55',
                            '#4dc0b5',
                            '#83BF6E',
                            '#5D5D5D'
                        ]
                    });
                }
            })
        })

        $(document).on('submit', '#create-edit-form', function (e) {
            e.preventDefault()

            let url = $(this).attr('action')
            let data = $(this).serialize()

            $.ajax({
                type: "POST",
                url: url,
                data: data,
                success: function (response) {
                    window.dtInstances['#return-status-table'].ajax.reload()

                    toastr.success(response.message)

                    $('#create-edit-modal').modal('hide')
                },
                error: function (response) {
                    appendValidationMessages($('#create-edit-modal'), response)
                }
            });
        })
    })
}
