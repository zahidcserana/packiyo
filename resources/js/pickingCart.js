window.PickingCart = function () {
    $(document).ready(function () {
        $(document).find('select:not(.custom-select)').select2();
    });

    window.datatables.push({
        selector: '#picking-cart-table',
        resource: 'picking-cart',
        ajax: {
            url: '/picking_carts/data-table'
        },
        order: [1, 'desc'],
        columns: [
            {
                "non_hiddable":true,
                "orderable": false,
                "class": "text-left",
                "title": "",
                "name": "picking_carts.id",
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
                "name": "picking_carts.name",
            },
            {
                "title": "Barcode",
                "data": "barcode",
                "name": "picking_carts.barcode",
                "class": "text-neutral-text-gray"
            },
            {
                "title": "Warehouse",
                "data": "warehouse",
                "name": "warehouse_contact_information.name",
                "class": "text-neutral-text-gray"
            },
            {
                "title": "Totes/shelves number",
                "data": "number_of_totes",
                "name": "number_of_totes",
                "class": "text-neutral-text-gray",
                "orderable": false
            },
            {
                "non_hiddable":true,
                "orderable": false,
                "class": "text-left",
                "title": "Print barcode",
                "name": "picking_carts.id",
                "data": function (data) {
                    return '<a href="' + data['link_print_barcode'] + '" target="_blank"  class="btn bg-logoOrange mx-auto px-5 text-white" style="display: inline-block"> Print Barcode </a>'
                }
            },
            {
                'non_hiddable': true,
                "orderable": false,
                "class": "text-right",
                "title": "",
                "name": "picking_carts.id",
                "data": function (data) {
                    return app.tableDeleteButton(
                        `Are you sure you want to delete ${data.name}?`,
                        data.link_delete
                    );
                }
            }
        ]
    })
}
