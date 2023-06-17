window.OrderStatus = () => {

    $(document).ready(function () {
        $(document).find('select:not(.custom-select)').select2();
    });

    window.datatables.push({
        selector: '#order-status-table',
        resource: 'order-status',
        ajax: {
            url: '/order_status/data-table'
        },
        order: [1, 'desc'],
        columns: [
            {
                "non_hiddable": true,
                "orderable": false,
                "class": "text-left",
                "title": "",
                "name": "id",
                "data": function (data) {
                    let editButton = `<a type="button" class="table-icon-button" data-id="${data.id}" href="${data.link_edit}">
                            <i class="picon-edit-filled icon-lg" title="Edit"></i>
                        </a>`;

                    return editButton;
                },
            },
            {
                "title": "Name",
                "data": "name",
                "name": "order_statuses.name",
            },
            {
                "title": "Customer",
                "data": function (data) {
                    return '<a href="' + data.customer.url + '" class="text-neutral-text-gray">' + data.customer.name + '</a>'
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
                'non_hiddable': true,
                "orderable": false,
                "class": "text-right",
                "title": "",
                "name": "action",
                "data": function (data) {
                    return app.tableDeleteButton(
                        `Are you sure you want to delete ${data.name}?`,
                        data.link_delete
                    );
                }
            }
        ]
    })

    $(document).ready(function() {
        $(document).find('select:not(.custom-select)').select2();

    })
}
