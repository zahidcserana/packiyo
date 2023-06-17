window.StaleInventoryReport = function () {
    const filterForm = $('#toggleFilterForm').find('form')
    window.loadFilterFromQuery(filterForm)
    const selector = '#stale_inventory-table';

    window.datatables.push({
        selector: selector,
        resource: 'stale_inventory',
        ajax: {
            url: '/report/stale_inventory/data_table',
            data: function(data){
                let request = window.serializeFilterForm(filterForm)

                data.filter_form = request

                window.queryUrl(request)

                window.exportFilters['stale_inventory'] = data
            }
        },
        order: [1, 'desc'],
        columns: [
            {
                'title': 'Product Name',
                'name': 'name',
                'data': function (data) {
                    return '<a href="' + data['product_url'] + '" target="_blank">' + data['product_name'] + '</a>';
                }
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
                'data': 'quantity_on_hand'
            },
            {
                'title': 'Last sold at',
                'name': 'last_sold_at',
                'data': 'last_sold_at'
            },
            {
                'title': 'Last sold',
                'name': 'last_sold_at',
                'data': 'last_sold'
            },
            {
                'title': 'Total amount sold',
                'name': 'amount_sold',
                'data': 'amount_sold'
            },
            {
                'title': 'Sold in the last 30 days',
                'name': 'sold_in_last_30_days',
                'data': 'sold_in_last_30_days'
            },
            {
                'title': 'Sold in the last 60 days',
                'name': 'sold_in_last_60_days',
                'data': 'sold_in_last_60_days'
            },
            {
                'title': 'Sold in the last 180 days',
                'name': 'sold_in_last_180_days',
                'data': 'sold_in_last_180_days'
            },
            {
                'title': 'Sold in the last 365 days',
                'name': 'sold_in_last_365_days',
                'data': 'sold_in_last_365_days'
            }
        ]
    })

    $(document).ready(function() {
        dateTimePicker();
        dtDateRangePicker();
        $(document).find('select:not(.custom-select)').select2();
    });
}
