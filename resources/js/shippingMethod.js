window.ShippingMethod = function () {

    $(document).ready(function () {
        $(document).find('select:not(.custom-select)').select2();
    });

    window.datatables.push({
        selector: '#shipping-method-table',
        resource: 'shipping_methods',
        ajax: {
            url: '/shipping_method/data-table'
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
                    return `
                        <a type="button" class="table-icon-button" data-id="${data.id}" href="${data.link_edit}">
                            <i class="picon-edit-filled icon-lg" title="Edit"></i>
                        </a>
                    `
                },
            },
            {
                "title": "Shop Shipping Method",
                "name": "name",
                "data": "name",
            },
            {
                "title": "Carrier",
                "name": "carrier_name",
                "data": "carrier_name",
                "orderable": false,
            },
            {
                "title": "Tags",
                "name": "tags",
                "orderable": false,
                "data": function (data) {
                    let tags = ''

                    data.tags.forEach(function (value, index, array) {
                        tags += `<span class="table-tag">${value}</span>`
                    })

                    return tags
                }
            },
        ],
    });
}
