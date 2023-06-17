window.PickingBatch = function () {
    let pickingBatch = $('#pickingBatchId').data('picking-batch')

    window.datatables.push({
        selector: '#picking-batch-items-table',
        resource: 'picking-batch-items',
        ajax: {
            url: '/picking_batch/' + pickingBatch + '/data_table'
        },
        order: [1, 'desc'],
        columns: [
            {
                'title': 'Order Number',
                'data': function (data) {
                    return `<a href='${data.order.edit_link}'>${data.order.number}</a>`
                },
                'name': 'orders.number'
            },
            {
                'title': 'SKU',
                'data': function (data) {
                    return `<a href='${data.product.edit_link}'>${data.product.sku}</a>`
                },
                'name': 'order_items.sku'
            },
            {
                'title': 'Batch Location',
                'data': function (data) {
                    return `<a href='${data.location.edit_link}'>${data.location.name}</a>`
                },
                'name': 'location.name'
            },
            {
                'title': 'Qty',
                'data': 'qty',
                'name': 'picking_batch_items.quantity',
            },
            {
                'title': 'Qty Picked',
                'data': 'qty_picked',
                'name': 'picking_batch_items.quantity_picked',
            },
            {
                'title': 'Tote',
                'data': function (data) {
                    let totes = ''

                    data.totes.forEach(function(tote) {
                        totes += `<a href='${tote.edit_link}'>${tote.name}</a>`
                    })

                    return totes
                },
                'name': 'totes',
                'sortable': false,
            },
            {
                'title': 'Picked From',
                'data': function (data) {
                    let locations = ''

                    data.picked_locations.forEach(function(location
                    ) {
                        locations += `<a href='${location.edit_link}'>${location.name}</a>`
                    })

                    return locations
                },
                'name': 'picked_locations',
                'sortable': false,
            },
            {
                'title': 'Picked at',
                'data': 'picked_time',
                'name': 'tote_order_items.picked_at',
            }
        ]
    })
};
