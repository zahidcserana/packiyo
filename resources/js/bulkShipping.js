window.BulkShipping = function (batchType = 'suggested') {

    $(document).ready(function () {
        dtDateRangePicker();
        dateTimePicker();

        $(document).find('select:not(.custom-select)').select2()
    })

    if (batchType === 'suggested') {
        columns = [
            {
                'orderable': true,
                'searchable': true,
                'title': 'Batch ID',
                'name': 'id',
                'data': 'id',
            },
            {
                orderable: true,
                searchable: true,
                title: 'Orders',
                name: 'total_orders',
                data: function (data) {
                    return `
                        <a href="${ data.bulk_ship_shipping_page_url }">${ data.total_orders }</a>
                    `
                },
            },
            {
                orderable: false,
                title: 'Items per order',
                data: function (data) {
                    let items = ''

                    data.order_items.map(function (item) {
                        items += `
                            <div class="d-inline-block position-relative">
                                <img
                                    src="${ item.image }"
                                    class="img-thumbnail"
                                    alt="Product image"
                                />
                                <span
                                    class="batch_order_item_quantity"
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    data-html="true"
                                    title="${ item.sku } - ${ item.name }"
                                >
                                    ${ item.quantity }
                                </span>
                            </div>
                        `
                    })

                    return items
                },
                name: 'items',
            },
            {
                title: 'Total items in batch',
                name: 'total_items',
                data: 'total_items',
                orderable: false,
            },
            {
                'orderable': true,
                'searchable': true,
                'title': 'Date',
                'name': 'created_at',
                'data': 'created_at',
            },
            {
                'title': 'Updated Date',
                'name': 'updated_at',
                'data': 'updated_at',
                'orderable': true,
            },
        ]
    } else {
        columns = [
            {
                title: 'ID',
                name: 'id',
                data: 'id',
                orderable: true,
            },
            {
                orderable: true,
                searchable: true,
                title: 'Orders',
                name: 'total_orders',
                data: function (data) {
                    let labels = ''

                    for (let i = 0; i < data.labels.length; i++) {
                        labels += '<a href="' + data.labels[i].url + '" target="_blank">' + data.labels[i].name + '</a><br />'
                    }

                    return `
                        <a href="${ data.label_pdf }" target="_blank" data-alert-title="Labels" data-alert-message="${escapeQuotes(labels)}" data-alert-icon-class="">${ data.total_orders }</a>
                    `
                },
            },
            {
                orderable: false,
                title: 'Items per order',
                data: function (data) {
                    let items = ''

                    data.order_items.map(function (item) {
                        items += `
                            <div class="d-inline-block position-relative">
                                <img
                                    src="${ item.image }"
                                    class="img-thumbnail"
                                    alt="Product image"
                                />
                                <span
                                    class="batch_order_item_quantity"
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    data-html="true"
                                    title="${ escapeQuotes(item.sku) } - ${ escapeQuotes(item.name) }"
                                >
                                    ${ item.quantity }
                                </span>
                            </div>
                        `
                    })

                    return items
                },
                name: 'items',
            },
            {
                title: 'Total items in batch',
                name: 'total_items',
                data: 'total_items',
                orderable: false,
            },
            {
                title: 'Date',
                name: 'created_at',
                data: 'created_at',
                orderable: true,
            },
            {
                title: 'Updated Date',
                name: 'updated_at',
                data: 'updated_at',
                orderable: true,
            },
            {
                title: 'Shipped at',
                orderable: true,
                searchable: true,
                name: 'shipped_at',
                data: 'shipped_at',
            },
            {
                title: "Printed",
                class: 'non-clickable',
                non_hiddable: true,
                orderable: false,
                searchable: false,
                data: function (data) {
                    if (data.printed_by) {
                        return data.printed_by
                    }

                    return `
                        <a
                            href="#"
                            class="btn bg-logoOrange text-white px-5 font-weight-700 mark-as-printed"
                            data-action="${data.mark_as_printed_url}"
                        >
                            Mark as printed
                        </a>
                    `
                },
            },
            {
                title: "Packed",
                class: 'non-clickable',
                non_hiddable: true,
                orderable: false,
                searchable: false,
                data: function (data) {
                    if (data.packed_by) {
                        return data.packed_by
                    }

                    return `
                        <a
                            href="#"
                            class="btn bg-logoOrange text-white px-5 font-weight-700 mark-as-packed"
                            data-action="${ data.mark_as_packed_url }"
                        >
                            Mark as packed
                        </a>
                    `
                },
            },
        ]
    }

    window.datatables.push({
        selector: '#bulk-shipping-table',
        resource: 'bulk-shipping',
        ajax: {
            url: window.location,
            data: function (data) {
                let request = {}
                $('#toggleFilterForm')
                    .find('form')
                    .serializeArray()
                    .map(function(input) {
                        request[input.name] = input.value;
                    });

                data.filter_form = request
            }
        },
        order: [0, 'desc'],
        columns: columns
    })

    $(document).on('click', '#bulk-shipping-table tbody tr', function (e) {
        if (! $(e.target).is('a,.non-clickable')) {
            $(this).find('a[href]')[0].click()
        }
    })

    $(document).on('click', '.mark-as-printed', function (e) {
        e.preventDefault()

        $.ajax({
            method: 'POST',
            url: $(this).data('action'),
            success: function (response) {
                toastr.success(response.message)
                window.dtInstances['#bulk-shipping-table'].ajax.reload()
            },
            error: function (response) {
                toastr.error(response.message)
            },
        })
    })

    $(document).on('click', '.mark-as-packed', function (e) {
        e.preventDefault()

        $.ajax({
            method: 'POST',
            url: $(this).data('action'),
            success: function (response) {
                toastr.success(response.message)
                window.dtInstances['#bulk-shipping-table'].ajax.reload()
            },
            error: function (response) {
                toastr.error(response.message)
            },
        })
    })
}
