window.LateOrders = function (event) {
    $(document).ready(function(e) {
        let columns = [

            {
                "title": "Number",
                "data": 'number',
                "name": "number"
            },
            {
                "title": "Required ship date",
                "name": "required_shipping_date_formatted",
                "data": "required_shipping_date_formatted"
            }
            ];

        $('#late-orders').DataTable(
            {
                serverSide: true,
                ajax: '/dashboard/late_orders/',
                responsive: true,
                pagingType: "simple_numbers",
                scrollX: true,
                ordering: false,
                paging: false,
                sDom: '<"top">rt<"bottom"<"col col-12"ip>>',
                createdRow: function( row, data, dataIndex ) {
                    $(row).attr( 'data-id', data['id'] );
                },
                initComplete: function (row, data, dataIndex) {
                    $('#total_late_orders').html(data.recordsTotal)
                },
                columns: columns,
            }
        );

        $(document).on('click', '#late-orders tbody tr', function (event) {
            if ($(this).find('.dataTables_empty').length) return false;

            let id = $(event.target).closest('tr').attr('data-id');
            window.location.href = '/order/' + id + '/edit';
        });
    });
};
