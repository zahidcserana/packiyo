window.ShippingBox = function () {
    $(document).ready(function () {
        $(document).find('select:not(.custom-select)').select2();
    });

    window.datatables.push({
        selector: '#shipping-box-table',
        resource: 'shipping-box',
        ajax: {
            url: '/shipping_box/data-table'
        },
        order: [1, 'desc'],
        columns: [
            {
                "non_hiddable": true,
                "orderable": false,
                "class": "text-left",
                "title": "",
                "name": "edit",
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
                "name": "name",
            },
            {
                "title": "Length",
                "data": "length",
                "name": "length",
            },
            {
                "title": "Width",
                "data": "width",
                "name": "width",
            },
            {
                "title": "Height",
                "data": "height",
                "name": "height",
            },
            {
                "title": "Customer",
                "data": function (data) {
                    return '<a href="'+ data.customer.url +'" class="text-neutral-text-gray">' + data.customer.name + '</a>';
                },
                "name": "customer.name",
                "class": "text-neutral-text-gray"
            },
            {
                'non_hiddable': true,
                "orderable": false,
                "class": "text-right",
                "title": "",
                "name": "delete",
                "data": function (data) {
                    return app.tableDeleteButton(
                        `Are you sure you want to delete ${data.name}?`,
                        data.link_delete
                    );
                }
            },
        ]
    })
};
