window.SearchGlobal = function () {
    let rowTpl = []
    let resultsMax = 0
    let keyword = ''
    let typeResults = []

    rowTpl['product'] = `
        <div class="row w-100 search_item_row p-3">
            <div class="col-2"><!--[IMAGE]--></div>
            <div class="col-6">
                <a href="<!--[LINK_EDIT]-->">
                    <!--[NAME]-->
                </a>
                <br>
                <!--[SKU]-->
            </div>
            <div class="col-4 text-right">
                On hand: <!--[ON-HAND]-->
                <br>
                Available: <!--[AVAILABLE]-->
            </div>
        </div>
    `

    rowTpl['order'] = `
        <div class="row w-100 search_item_row p-3">
            <div class="col-6">
                <a href="<!--[LINK_EDIT]-->">
                    <!--[NUMBER]-->
                </a>
            </div>
            <div class="col-6 text-right"><!--[DATE]--></div>
        </div>
    `

    rowTpl['purchase_orders'] = `
        <div class="row w-100 search_item_row p-3">
            <div class="col-2">
                <a href="<!--[LINK_EDIT]-->">
                    <!--[NUMBER]-->
                </a>
            </div>
            <div class="col-6"><!--[VENDOR]--></div>
            <div class="col-4 text-right"><!--[ORDER-DATE]--></div>
        </div>
    `

    rowTpl['return'] = `
        <div class="row w-100 search_item_row p-3">
            <div class="col-2"><!--[CREATED-AT]--></div>
            <div class="col-8">
                <a href="<!--[LINK_EDIT]-->">
                    <!--[ORDER-NUMBER]-->
                </a>
            </div>
            <div class="col-2 text-right"><!--[ORDER-ORDER-DATE]--></div>
        </div>
    `

    function searchItems(keyword, type) {
        typeResults[type] = 0

        $.ajax({
            url: '/' + type + '/data-table',
            type: 'GET',
            dataType: 'json',
            data: {
                search: {
                    'value': keyword,
                    'regex': false
                },
                filter_form: {
                    'allocated': '',
                    'backordered': '',
                    'in_stock': '',
                    'supplier': '',
                    'warehouse': '',
                    'is_kit': '',
                    'start_date': '',
                    'end_date': '',
                    'order_status': '',
                    'created_date': '',
                    'ready_to_ship': '',
                    'priority_from': '',
                    'priority_to': '',
                    'country': '',
                    'shipping_method': '',
                    'weight_from': '',
                    'weight_to': '',
                    'carriers': '',
                    'return_status': '',
                    'purchase_order_status': '',
                    'sku': ''
                },
                start: 0,
                length: 10
            },
            success: function (response) {
                drawResults(response, type)
            }
        })
    }

    function drawResults(response, type) {
        let filteredRecords = response.data.length;
        let searchTotalText = 'No matching records found'
        if (filteredRecords > 0) {
            searchTotalText = filteredRecords + ' Search Results'
        }

        typeResults[type] = filteredRecords

        if (filteredRecords > resultsMax) {
            resultsMax = filteredRecords
            $(`#${type}_tab_open`).trigger('click')
        }

        $(`#${type}_search_total`).html(searchTotalText)
        $(`#${type}_search_container`).html('')

        $(`#${type}_see_all`).attr('href', `/${type}/search/${keyword}`)

        response.data.map(function (record) {
            let rowTplHTML = rowTpl[type]

            if (record.name) {
                rowTplHTML = rowTplHTML.replace('<!--[NAME]-->', record.name)
                rowTplHTML = rowTplHTML.replace('<!--[LINK_EDIT]-->', record.link_edit)
            }

            if (record.sku) {
                rowTplHTML = rowTplHTML.replace('<!--[SKU]-->', record.sku)
            }

            if (record.quantity_available !== undefined) {
                rowTplHTML = rowTplHTML.replace('<!--[AVAILABLE]-->', parseInt(record.quantity_available))
            }

            if (record.quantity_on_hand !== undefined) {
                rowTplHTML = rowTplHTML.replace('<!--[ON-HAND]-->', parseInt(record.quantity_on_hand))
            }

            if (record.number) {
                rowTplHTML = rowTplHTML.replace('<!--[NUMBER]-->', record.number)
                rowTplHTML = rowTplHTML.replace('<!--[LINK_EDIT]-->', record.link_edit)
            }

            if (record.ordered_at) {
                rowTplHTML = rowTplHTML.replace('<!--[ORDER-DATE]-->', record.ordered_at)
            }

            if (record.date) {
                rowTplHTML = rowTplHTML.replace('<!--[DATE]-->', record.date)
            }

            if (record.order && record.order.number) {
                rowTplHTML = rowTplHTML.replace('<!--[ORDER-NUMBER]-->', record.order.number)
                rowTplHTML = rowTplHTML.replace('<!--[LINK_EDIT]-->', record.link_edit)
            }

            if (record.order && record.order.created_at) {
                rowTplHTML = rowTplHTML.replace('<!--[ORDER-ORDER-DATE]-->', record.order.created_at)
            }

            if (record.created_at) {
                rowTplHTML = rowTplHTML.replace('<!--[CREATED-AT]-->', record.created_at)
            }

            if (record.warehouse && record.warehouse.name) {
                rowTplHTML = rowTplHTML.replace('<!--[VENDOR]-->', record.warehouse.name)
            }

            rowTplHTML = rowTplHTML.replace(
                '<!--[IMAGE]-->',
                `<img src="${record.image ?? '/img/no-image.png'}" class="img-thumbnail">`
            )

            if (record.price) {
                rowTplHTML = rowTplHTML.replace('<!--[PRICE]-->', record.price)
            }

            let oneRow = rowTplHTML

            $(`#${type}_search_container`).append(oneRow)
        })
    }

    $(document).ready(function () {
        $('.search_res_tab').click(function () {
            let relation = $(this).attr('rel')

            if (typeResults[relation] !== undefined && parseInt(typeResults[relation]) > 0) {
                $('.search_result_box').each(function () {
                    $(this).hide()
                })

                let relationSearchBlock = $(`#${relation}_search_block`)

                relationSearchBlock.show()

                if (relationSearchBlock.hasClass('d-none')) {
                    relationSearchBlock.removeClass('d-none')
                }
            }
        })

        $('#search_close').click(function () {
            $('#global_search_results').toggleClass('d-none')

            $('#search_input_container')
                .removeClass('input-group-active')
                .addClass('input-group')

            $('#global_search_input').val('')
        })

        $('#global_search_input').keyup(function () {
            resultsMax = 0
            keyword = $(this).val().trim()

            if (keyword !== '') {
                searchItems(keyword, 'product')
                searchItems(keyword, 'order')
                searchItems(keyword, 'return')
                searchItems(keyword, 'purchase_orders')

                if ($('#global_search_results').hasClass('d-none')) {
                    $('#global_search_results').removeClass('d-none')
                    $('#search_input_container')
                        .removeClass('input-group')
                        .addClass('input-group-active')
                }
            }
        })
    })
}
