window.PackingSingleOrderShipping = function () {
    const filterForm = $('#toggleFilterForm').find('form')

    $(document).ready(function () {
        $(document).find('select:not(.custom-select)').select2();
    });

    window.datatables.push({
        selector: '#packing-single-order-shipping-table',
        resource: 'packing-single-order-shipping',
        ajax: {
            url:'/packing/single_order_shipping/',
            data: function (data) {
                let request = {}
                filterForm
                    .serializeArray()
                    .map(function(input) {
                        request[input.name] = input.value;
                    });

                data.filter_form = request
                data.from_date = $('#packing-single-order-shipping-table-date-filter').val()
            }
        },
        order: [0, 'desc'],
        columns: [
            {
                "class":"text-left",
                "title": "Order",
                "name":"number",
                "data": function (data) {
                    let tooltipTitle = '';
                    data.order_products.map(function(orderProduct){
                        tooltipTitle += orderProduct.quantity + ' - ' + orderProduct.sku + ' (' + orderProduct.name + ')<br/>';
                    });

                    return `<span class="order_number_container d-flex" packing_url="${data.link_packing}">
                        <i class="picon-alert-circled-light mr-1" data-toggle="tooltip" data-placement="top" data-html="true" title="${escapeQuotes(tooltipTitle)}"></i>
                         <span class="order_number_link" order_url="${data.link_order}">${data.number}</span>
                    </span>`
                },
            },
            {
                "class": "text-neutral-text-gray",
                "title": "Tote",
                "name": "tote",
                "data": "tote",
                "orderable": false
            },
            {
                "class": "text-neutral-text-gray",
                "title": "Items",
                "name": "order_items_count",
                "data": "items_count",
            },
            {
                "orderable": false,
                "class": "text-neutral-text-gray",
                "title": "Preview",
                "data": function (data) {

                    let imgPreview = ''

                    data.items_images.map(function( items_image ){

                        imgPreview += '<img src="'+items_image+'" class="img-thumbnail" />'
                    });

                    return imgPreview
                },
                "name": "preview"
            },
            {
                "class": "text-neutral-text-gray",
                "title": "Required ship date",
                "data": function (data) {
                    return data.shipping_date_before_at
                },
                "name": "shipping_date_before_at"
            },
            {
                "class": "text-neutral-text-gray",
                "title": "Country",
                "name": "country",
                "data": "country",
            }
        ]
    })

    $(document).ready(function() {
        dateTimePicker();
        dtDateRangePicker();
        let search = $('#packing-single-order-shipping-table-container .searchText')

        search.on('keyup', $.fn.debounce(() => {
            let term = encodeURIComponent(search.val());

            if (term && term.length >= 3) {
                $.ajax({
                    type: 'GET',
                    serverSide: true,
                    url: '/packing/single_order_shipping/barcode_search/' + term,

                    success: function(data) {
                        if (data.success) {
                            window.location.href = data.redirect
                        }
                    },
                });
            }
        }));

        $(document).on('click', '#packing-single-order-shipping-table tbody tr', function (event) {
            if ( !$(event.target).hasClass('picon-alert-circled-light') && !$(event.target).hasClass('order_number_link') && document.getSelection().toString() === '' ) {
                const url = $(event.target).parent().find('.order_number_container').attr('packing_url')

                if (url) {
                    window.location.href = $(event.target).parent().find('.order_number_container').attr('packing_url')
                }
            }
        });

        $(document).on('click', '.order_number_link', function (event) {
            window.location.href = $(this).attr('order_url');
        });

        $('.created-date-filter').select2();
    })
}
