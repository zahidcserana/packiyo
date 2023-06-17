window.SerialNumberReport = function () {
    const filterForm = $('#toggleFilterForm').find('form')
    window.loadFilterFromQuery(filterForm)
    const selector = '#serial_number-table';

    window.datatables.push({
        selector: selector,
        resource: 'serial_number',
        ajax: {
            url: '/report/serial_number/data_table',
            data: function (data) {
                let request = window.serializeFilterForm(filterForm)

                data.filter_form = request

                window.queryUrl(request)

                window.exportFilters['serial_number'] = data
            }
        },
        columns: [
            {
                "title": "SKU",
                "data": function (data) {
                    return `<a href="${data.product.url}">${data.product.sku}</a>`;
                },
                "name": "products.sku"
            },
            {
                'title': 'Product name',
                "data": function (data) {
                    return `<a href="${data.product.url}">${data.product.name}</a>`;
                },
                "name": "products.name",
                "visible": false
            },
            {
                'title': 'Order number',
                "data": function (data) {
                    return `
                        <a href="${data.order.url}">${data.order.number}</a>
                    `
                },
                'name': 'orders.number',
            },
            {
                'title': 'Serial number',
                'name': 'serial_number',
                'data': 'serial_number',
            },
            {
                'hidden_when_load': true,
                "title": "Date shipped",
                "data": "date_shipped",
                "name": "shipments.created_at",
                "visible": false
            }
        ],
    })

    $(document).ready(function () {
        dateTimePicker();
        dtDateRangePicker();
        $(document).find('select:not(.custom-select)').select2();
    })
}
