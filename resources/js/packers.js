window.PackerReport = function () {
    const filterForm = $('#toggleFilterForm').find('form')
    window.loadFilterFromQuery(filterForm)
    const selector = '#packer-table';

    window.datatables.push({
        selector: selector,
        resource: 'packer',
        ajax: {
            url: '/report/packer/data_table',
            data: function(data){
                let request = window.serializeFilterForm(filterForm)

                data.filter_form = request;

                window.queryUrl(request);

                window.exportFilters['packer'] = data;
            }
        },
        order: [0, 'asc'],
        columns: [
            {
                'title': 'Name',
                'name': 'contact_informations.name',
                'data': 'name',
            },
            {
                'orderable': true,
                'hidden_when_load': true,
                'title': 'Shipments',
                'data': 'shipments_count',
                'name' : 'shipments_count',
            },
            {
                'orderable': true,
                'hidden_when_load': true,
                'title': 'Items',
                'data': 'items_count',
                'name' : 'items_count',
            },
            {
                'orderable': true,
                'hidden_when_load': true,
                'title': 'Unique Items',
                'data': 'unique_items_count',
                'name' : 'unique_items_count',
            },
            {
                'orderable': true,
                'hidden_when_load': true,
                'title': 'Orders',
                'data': 'orders_count',
                'name' : 'orders_count',
            }
        ],
    })

    $(document).ready(function() {
        dateTimePicker();
        dtDateRangePicker();

        $(document).find('select:not(.custom-select)').select2();
    })
}
