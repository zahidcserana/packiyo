window.ReplenishmentReport = function () {
    const filterForm = $('#toggleFilterForm').find('form')
    window.loadFilterFromQuery(filterForm)
    const selector = '#replenishment-table';

    window.datatables.push({
        selector: selector,
        resource: 'replenishment',
        ajax: {
            url: '/report/replenishment/data_table',
            data: function(data){
                let request = window.serializeFilterForm(filterForm)

                data.filter_form = request

                window.queryUrl(request)

                window.exportFilters['replenishment'] = data
            }
        },
        order: [1, 'desc'],
        columns: [
            {
                'title': 'Product Name',
                'name': 'name',
                'data': function (data) {
                    return '<a href="' + data['product_url'] + '" target="_blank">' + data['product_name'] + '</a>';
                },
            },
            {
                'title': 'SKU',
                'name': 'sku',
                'data': function (data) {
                    return '<a href="' + data['product_url'] + '" target="_blank">' + data['sku'] + '</a>';
                }
            },
            {
                'title': 'On Hand',
                'name': 'quantity_on_hand',
                'data': 'quantity_on_hand',
            },
            {
                'title': 'Allocated',
                'name': 'quantity_allocated',
                'data': 'quantity_allocated',
            },
            {
                'title': 'Pickable amount',
                'name': 'quantity_pickable',
                'data': 'quantity_pickable',
            },
            {
                'title': 'QTY to move',
                'data': 'qty',
                'orderable': false,
            }
        ]
    })

    $(document).ready(function() {
        dateTimePicker();
        dtDateRangePicker();
        $(document).find('select:not(.custom-select)').select2();
    });
}
