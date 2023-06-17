window.GlobalSearch = function (keyword) {

    let recordsFiltered = 0;
    let tbSdom = '<"row view-filter"<"col-sm-12"<"clearfix">>>t<"row view-pager"<"col-sm-12"<"text-center"ip>>>';

    function ajaxPart(url) {
        return {
            url: url,
            data: function (data) {
                //
            }
        }
    }

    function searchPart(){
        return  {
            "search": keyword
        };
    }

    function setLoadedResultsNum(settings, type) {
        let response = settings.json;

        let results = parseInt(response.data.length);
        recordsFiltered += results;

        if (results === 0) {
            $('#view_more_' + type).hide();
        } else {
            $('#show_all_' + type).html(results);
        }

        $('#search_result_num').html(recordsFiltered)
    }

    $(document).ready(function() {
        let searchOrderTable = $('#search-orders-table').DataTable({
            search:searchPart(),
            serverSide: true,
            processing: true,
            ajax: ajaxPart( '/order/data-table' ),
            responsive: true,
            lengthChange: false,
            info: false,
            language: window.datatableGlobalLanguage,
            sDom: tbSdom,
            drawCallback: function( settings ) {
                setLoadedResultsNum(settings, 'orders')
            },
            columns: [
                {
                    "title": "Number",
                    "name": "number",
                    "data": function (data) {
                        return `
                            <a href="${data.link_edit}" target="_blank">${data.number}</a>
                        `
                    }
                },
                {
                    "title": "Shipped by",
                    "data": function (data) {
                        return '<a class="text-neutral-text-gray" href="' + data.customer['url'] + '">' + data.customer['name'] + '</a>'
                    },
                    "name": "customer_contact_information.name"
                },
                {
                    "title": "Status",
                    "data": "order_status_name",
                    "name": "order_status_id",
                    "class" : "orderStatus",
                    render : function(data, type, row) {
                        return '<span class="statusGreen">' + data + '</span>'
                    }
                },
                {
                    "title": "Date",
                    "data": "ordered_at",
                    "name": "orders.updated_at"
                },
                {
                    "title": "Tracking Number",
                    "data": "tracking_number",
                    "name": "tracking_number",
                    "className": "trackingNumberData",
                    render : function(data, type, row) {
                        return `
                            <span class="trackingNumber">
                                <div class="d-flex align-items-center">
                                    <i class="picon-alert-circled-light mr-1"></i>
                                </div>
                                <span class="number">
                                    ${data}
                                </span>
                            </span>`
                    }
                },

            ],
            aaSorting: [],
        });

        let searchReturnTable = $('#search-returns-table').DataTable(
            {
                search: searchPart(),
                order: [3, 'desc'],
                serverSide: true,
                processing: true,
                ajax: ajaxPart( '/return/data-table' ),
                responsive: true,
                lengthChange: false,
                info: false,
                language: window.datatableGlobalLanguage,
                sDom: tbSdom,
                drawCallback: function( settings ) {
                    setLoadedResultsNum(settings, 'returns');
                },
                columns: [
                    {
                        "title": "RMA Number",
                        "name": "returns.number",
                        "data": function (data) {
                            return `
                                <a href="${data.link_edit}" target="_blank">${data.number}</a>
                            `
                        }
                    },
                    {
                        "title": "Status",
                        "name": "returns.returnStatus",
                        "data": function(data) {
                            let status = ''

                            if (data.returnStatus) {
                                status = data.returnStatus.name
                            }

                            if (status === 'Approved') {
                                return '<div class="status active">Approved</div>';
                            } else if (status !== '') {
                                return '<div class="status pending">' + status + '</div>';
                            }

                            return '<div class="status">n/a</div>';
                        }
                    },
                    {
                        "title": "Order Number",
                        "name": "orders.number",
                        "data": (data) => {
                            let icon = `<i class="picon-alert-circled-light"></i>`

                            return '<div class="info">' + icon + data.order.number + '</div>'
                        }
                    },
                    {
                        "title": "RMACreated",
                        "name": "returns.created_at",
                        "data": "created_at"
                    },
                    {
                        "title": "City",
                        "name": "contact_informations.city",
                        "data": "city",
                    },
                    {
                        "title": "ZIP",
                        "name": "contact_informations.zip",
                        "data": "zip"
                    }
                ],
                createdRow: function(row, data, dataIndex){
                    $('td:eq(2)', row).css('min-width', '200px');
                }
            }
        );

        let searchPurchaseOrdersTable = $('#search-purchase-orders-table').DataTable(
            {
                search:searchPart(),
                serverSide: true,
                processing: true,
                dropdownAutoWidth : true,
                order: [],
                ajax: ajaxPart( '/purchase_orders/data-table' ),
                responsive: true,
                info: false,
                language: window.datatableGlobalLanguage,
                sDom: tbSdom,
                drawCallback: function( settings ) {
                    setLoadedResultsNum(settings, 'purchase_orders');
                },
                columns: [
                    {
                        "title": "PO number",
                        "name": "number",
                        "width": "30%",
                        "data": function (data) {
                            return `
                                <a href="${data.link_edit}" target="_blank">${data.number}</a>
                            `
                        }
                    },
                    {
                        "title": "Status",
                        "data": "status",
                        "name": "purchase_order_statuses.name"
                    },
                    {
                        "title": "Created date",
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
                        "data": function (data) {
                            return data.supplier['name']
                        }
                    },
                    {
                        "title": "Warehouse",
                        "name": "warehouse_contact_information.name",
                        "data": function (data) {
                            return data.warehouse['name']
                        }
                    },
                    {
                        "title": "Receive PO",
                        "orderable": false,
                        "data": function (data) {
                            return '<div class="text-center">' +
                                '<a href="' + data.link_receive['url'] + '">' +
                                '<img width="10%" height="auto" src="' + data.link_receive['img'] + '">' +
                                '</a>' +
                                '</div>'
                        }
                    }
                ],
            }
        );

        let searchInventoryTable = $('#search-products-table').DataTable({
            search:searchPart(),
            serverSide: true,
            processing: true,
            order: [],
            ajax: ajaxPart('/product/data-table'),
            responsive: true,
            lengthChange: false,
            info: false,
            language: window.datatableGlobalLanguage,
            sDom: tbSdom,
            drawCallback: function( settings ) {
                setLoadedResultsNum(settings, 'inventory');
            },
            columns: [
                {
                    "title": "Name",
                    "data": "name",
                    "name": "products.name",
                    "data": function (data) {
                        return `
                            <a href="${data.link_edit}" target="_blank">${data.name}</a>
                        `
                    }
                },
                {
                    "title": "On Hand",
                    "data": "quantity_on_hand",
                    "name": "products.quantity_on_hand",
                },
                {
                    "title": "SKU",
                    "data": "sku",
                    "name": "products.sku",
                },
                {
                    "title": "Available",
                    "data": "quantity_available",
                    "name": "products.quantity_available"
                },
                {
                    "title": "Price",
                    "data": "price",
                    "name": "products.price",
                },
                {
                    "title": "Barcode",
                    "data": "barcode",
                    "name": "products.barcode",
                    'visible': false
                }
            ]
        });

        $('.view-pager').hide();
    });
};
