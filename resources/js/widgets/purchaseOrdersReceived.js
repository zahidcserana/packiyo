window.PurchaseOrdersReceived = function (event) {
    $(document).ready(function(e) {
        let columns = [

            {
                "title": "Number",
                "data": 'number',
                "name": "number"
            },
            {
                "title": "Ordered date",
                "name": "ordered_at",
                "data": "ordered_at"
            },
            {
                "title": "Expected date",
                "name": "expected_at",
                "data": "expected_at"
            },
            {
                "title": "Vendor",
                "name": "supplier_contact_information.name",
                "data": 'name'
            }
        ];

        $('#purchase-orders-received').DataTable(
            {
                serverSide: true,
                ajax: '/dashboard/purchase_orders_received/',
                responsive: true,
                pagingType: "simple_numbers",
                scrollX: true,
                pageLength: 5,
                paging: false,
                ordering: false,
                sDom: '<"top">rt<"bottom"<"col col-12"ip>>',
                createdRow: function( row, data, dataIndex ) {
                    $(row).attr( 'data-id', data['id'] );
                },
                columns: columns,
            }
        );

        $(document).on('click', '#purchase-orders-received tbody tr', function (event) {
            if ($(this).find('.dataTables_empty').length) return false;

            let id = $(event.target).closest('tr').attr('data-id');
            window.location.href = '/purchase_orders/' + id + '/edit'
        });
    });
};
