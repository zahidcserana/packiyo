window.ReturnOrder = function (keyword='') {
    const filterForm = $('#toggleFilterForm').find('form')
    window.loadFilterFromQuery(filterForm)

    const tableSelector = 'returns';
    $(document).ready(function () {

        if(keyword!=''){
            $('.searchText').val(keyword);
            window.dtInstances['#'+tableSelector+'-table'].search(keyword).draw();
        }
    });

    window.datatables.push({
        selector: '#'+tableSelector+'-table',
        resource: 'returns',
        ajax: {
            url: '/return/data-table',
            data: function (data) {
                let request = window.serializeFilterForm(filterForm)

                data.filter_form = request

                window.queryUrl(request)
            }
        },
        order: [4, 'desc'],
        columns: [
            {
                "orderable": false,
                "class": "text-left",
                "createdCell": (cell) => {
                    $(cell).addClass("d-flex")
                },
                "title": `<div class="custom-datatable-checkbox-container-header">
                            <div>
                                <input id="select-all-checkboxes" type="checkbox" value="0">
                                <label for="select-all-checkboxes"></label>
                            </div>
                          </div>`,
                "name": "returns.id",
                "data": function (data) {
                    return `
                        <div class="custom-datatable-checkbox-container">
                            <div>
                                <input name="bulk-edit[${data.id}]" id="bulk-edit-${data.id}" class="custom-datatable-checkbox" type="checkbox" value="0">
                                <label class="mb-0" for="bulk-edit-${data.id}"></label>
                            </div>
                        </div>
                        <button type="button" class="table-icon-button" data-id="${data.id}" data-toggle="modal" data-target="#return-show">
                            <i class="picon-show-light icon-lg"></i>
                        </button>
                        <button type="button" class="table-icon-button" data-id="${data.id}">
                            <a href="/return/${data.id}/edit">
                                <i class="picon-edit-filled icon-lg" title="Edit"></i>
                            </a>
                        </button>
                    `
                },
            },
            {
                "title": "RMA Number",
                "name": "returns.number",
                "data": function (data) {
                    return `
                        <a href="#" data-id="${data.id}" data-toggle="modal" data-target="#return-status">
                            ${data.number}
                        </a>
                    `
                }
            },
            {
                "title": "Status",
                "name": "return_status_id",
                "data": function(data) {
                    let status = ''
                    let color = ''

                    status = data.returnStatus
                    color = data.returnStatusColor

                    if (status !== '') {
                        return `
                            <div class="status">
                                <span class="statusBg p-1 border-5" style="background-color: ${color}">
                                    <span style="opacity: 0">
                                        ${status}
                                    </span>
                                </span>
                                <span class="statusText" style="color: ${color} ">
                                    ${status}
                                </span>
                            </div>
                        `
                    }

                    return '<div class="status">n/a</div>';
                }
            },
            {
                "title": "Order Number",
                "name": "orders.number",
                "data": (data) => {
                    let productList = data.returnItems.join("\n")

                    let tooltipTitle = '';
                    data.order_products.map(function(orderProduct){
                        tooltipTitle += orderProduct.quantity + ' - ' + orderProduct.sku + ' (' + orderProduct.name + ')<br/>';
                    });

                    return `
                        <span title="${productList}">
                            <i class="picon-alert-circled-light mr-1" data-toggle="tooltip" data-placement="top" data-html="true" title="${escapeQuotes(tooltipTitle)}"></i>
                        </span>
                        <a class="ml-1" href="${data.order.url}" target="_blank">
                            ${data.order.number}
                        </a>
                    `
                }
            },
            {
                "title": "RMA Created",
                "name": "returns.created_at",
                "data": "created_at",
            },
            {
                "title": "Return Reason",
                "name": "reason",
                "data": "reason",
            },
            {
                'title': 'Tracking numbers',
                'name': 'tracking_number',
                'data': 'tracking_number',
                'orderable': false
            },
            {
                'title': 'Labels',
                'name': 'return_labels',
                'data': 'return_labels',
                'orderable': false
            },
        ]
    })

    $(document).ready(function() {
        dateTimePicker();
        dtDateRangePicker();

        $(document).find('select:not(.custom-select)').select2();

        $('#return-show').on('show.bs.modal', function (e) {
            $('#return-show .modal-content').html(`<div class="spinner">
                <img src="../../img/loading.gif">
            </div>`)
            let itemId = $(e.relatedTarget).data('id');

            $.ajax({
                type:'GET',
                serverSide: true,
                url:'/return/' + itemId,

                success: function(data) {
                    $('#return-show > div').html(data);
                    app.initTags();
                },
            })
        })

        $('#return-status').on('show.bs.modal', function (e) {
            $('#return-status .modal-content').html(`<div class="spinner">
                <img src="../../img/loading.gif">
            </div>`)
            let itemId = $(e.relatedTarget).data('id');

            $.ajax({
                type:'GET',
                serverSide: true,
                url:'/return/status/' + itemId,

                success:function(data) {
                    $('#return-status > div').html(data);
                    app.initTags();

                    $(document).find('select.getFilteredStatuses').select2({
                        dropdownParent: $('#return-status-form')
                    });
                },
            });
        })

        $(document).find('select.getFilteredOrders').select2();

        $(document).find('select.getFilteredWarehouses').select2();

        $(document).find('select.getOrderProductsFiltered').select2();

        $(document).on('show.bs.tab', '#products-tab', function (e) {
            let orders = $('select.getFilteredOrders').find(':selected').length
            let warehouses = $('select.getFilteredWarehouses').find(':selected').length
            let errors = {}

            if (! orders) {
                errors.order_id = 'The order field is required.'
            }

            if (! warehouses) {
                errors.warehouse_id = 'The warehouse field is required.'
            }

            if (! (orders && warehouses)) {
                e.preventDefault()

                appendValidationMessages(
                    $(this).parents('form'), {
                        responseJSON: {
                            errors: errors,
                            messages: "The given data was invalid."
                        }
                    }
                )
            }
        })

        $(document).on('submit', '#return-create-form', function (e) {
            e.preventDefault()

            var modal = $('#returnCreateModal')
            var form = $(this)
            var data = form.serialize()

            $.ajax({
                type: "POST",
                url: "/return",
                data: data,
                success: function () {
                    resetModalWithForm(modal)
                    clearValidationMessages(modal)
                    window.dtInstances['#returns-table'].ajax.reload()
                },
                error: function (response) {
                    appendValidationMessages(modal, response)
                }
            });
        })

        $(document).on('submit', '#return-status-form', function (e) {
            e.preventDefault()

            var modal = $('#return-status')
            var form = $(this)
            var data = form.serialize()
            var action = form.attr('action')

            $.ajax({
                type: "PUT",
                url: action,
                data: data,
                success: function () {
                    resetModalWithForm(modal)
                    window.dtInstances['#returns-table'].ajax.reload()
                },
                error: function (response) {
                    appendValidationMessages(modal, response)
                }
            })
        })

        $('select.getFilteredOrders').on('select2:select', function (e) {
            let order_id = $(this).val()

            $.ajax({
                type:'GET',
                serverSide: true,
                url:'/return/filterOrderProducts/' + order_id,

                success:function(data) {
                    let orderItemsContainer = $('#order_items_container')
                    orderItemsContainer.html('')

                    $(data.results).each(function (i, product) {
                        orderItemsContainer.append(
                            `<tr>
                                <td><img class="return_image_preview" src="` + product.image  + `" alt=""></td>
                                <td>` + product.text + `</td>
                                <td>` + product.quantity + `</td>
                                <td>
                                    <div class="input-group input-group-alternative input-group-merge">
                                        <input
                                            class="form-control font-weight-600 text-black h-auto p-2"
                                            name="items[`+ product.id +`][quantity]"
                                            type="number"
                                            value="0"
                                            max="`+ product.quantity + `"
                                            min="0"
                                        >
                                         <input name="items[`+ product.id +`][is_returned]"
                                                       type="hidden" value="`+ product.id +`" checked="checked">
                                    </div>
                                </td>
                            </tr>`
                        )
                    })
                },
            });
        });

        $(document).find('select').on('select2:select', function (e) {
            let name = $(e.target).attr('data-name');
            let hiddenText =$("input[name="+ name + "_text]");

            if(hiddenText.length) {
                hiddenText.val(e.params.data.text)
            }
        });

        $('#bulk-edit-modal').on('show.bs.modal', function () {
            let ids = []
            let form = $('#bulk-edit-form')

            $('input[name^="bulk-edit"]').each(function() {
                if($(this).prop('checked')) {
                    let returnId = $(this).attr('name')
                    returnId = returnId.replace(/[^0-9]/g,'')

                    ids.push(parseInt(returnId))
                }
            })

            $('#number-of-selected-items').text(ids.length)
            $('#item-type').text('Returns')
            $('#model-ids').val(ids)

            form.attr('action', '/return/bulk-edit')
            form.serialize()
        })
    });

    function openCreationModal() {
        let hash = window.location.hash;

        if (hash && hash === '#open-modal') {
            $(document).find('#returnCreateModal').modal('show')
            window.location.hash = '';
        }
    }

    openCreationModal();

    $(document).on('click', '.openCreateModal', function () {
        openCreationModal();
    })

    $(window).on('hashchange', function (e) {
        openCreationModal();
    })

    if( keyword !='' ){
        table.search(keyword).draw();
    }
}
