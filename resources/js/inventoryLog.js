window.InventoryLog = function (keyword='') {
    const filterForm = $('#toggleFilterForm form');
    window.loadFilterFromQuery(filterForm)
    filterForm.find('select').select2();

    $(document).ready(function () {
        $(document).find('select:not(.custom-select)').select2();
    });

    window.datatables.push({
        selector: '#inventory-log-table',
        resource: 'inventory_logs',
        ajax: {
            'url': '/inventory_log/data-table',
            'data': function(data) {
                let request = window.serializeFilterForm(filterForm)

                data.filter_form = request

                window.queryUrl(request)

                window.exportFilters['inventory_logs'] = data
            }
        },
        columns: [
            {
                "title": "Customer",
                "data": function (data) {
                    return data.customer['name'];
                },
                "name": "customer_contact_information.name",
                orderable: false,
            },
            {
                "title": "Date",
                "data": "created_at",
                "name": "inventory_logs.created_at"
            },
            {
                "title": "Warehouse",
                "data": function(data) {
                    return data.warehouse['name'];
                    // return '<a href="' + data.warehouse['url'] + '" style="display: inline-block">'+ data.warehouse['name'] + '</a>';
                },
                "name": "warehouse_contact_informations.name",
                orderable: false,
            },
            {
                "title": "SKU",
                "data": "sku",
                "name": "products.sku",
                "class": "text-center"
            },
            {
                "title": "Product Name",
                "name": "products.name",
                "data": function (data) {
                    return data.product['url'] === '#' ? 'Deleted/' + data.product['name'] : data.product['url'];
                }
            },
            {
                "title": "Location",
                "name": "locations.name",
                "data": "location",
                orderable: false,
            },
            // {
            //     "title": "Associated Object",
            //     "name": "inventory_logs.associated_object_type",
            //     "data": function(data) {
            //         return data.associated_object;
            //     }
            // },
            {
                "title": "Previous On Hand",
                "data": "previous_on_hand",
                "name": "inventory_logs.previous_on_hand",
                "class": "text-center",
            },
            {
                "title": "New On Hand",
                "data": "new_on_hand",
                "name": "inventory_logs.new_on_hand",
                "class": "text-center",
            },
            {
                "title": "Reason",
                "data": "reason",
                "name": "reason"
            },
            {
                "title": "Changed By",
                "name": "users_contact_information.name",
                "data": function (data) {
                    return data.user['name'];
                },
                orderable: false,
            },
        ],
        createdRow: function(row, data, dataIndex){
            $('td:eq(0)', row).css('min-width', '100px');
        },
    })

    $(document).ready(function() {
        dateTimePicker();
        dtDateRangePicker();
        let etable = window.dtInstances['#inventory-log-table'];

        $(document).on('change', '.colvisItem', function () {
            let index = $(this).data('index');

            if($(this).is(':checked')){
                etable.column(index).visible(true)
            } else {
                etable.column(index).visible(false)
            }
        });

        $('.export-inventory-log').click(function () {
            $('#export-inventory-log-modal').modal('toggle')
        })
    })
}
