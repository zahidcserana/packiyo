window.Product = function (product = '', lotTracking = 0) {
    const tableSelector = 'products';
    const lotTdsClasses = 'd-table-cell';
    const lotTdsDivClasses = 'pt-0 pr-0 pb-4 pl-0';

    $(document).ready(function () {
        $(document).find('select:not(.custom-select)').select2();
    })

    if ($('#products-table').length) {
        const filterForm = $('#toggleFilterForm').find('form')
        window.loadFilterFromQuery(filterForm)

        window.datatables.push({
            selector: '#' + tableSelector + '-table',
            resource: 'products',
            ajax: {
                url: '/product/data-table',
                data: function (data) {
                    let request = window.serializeFilterForm(filterForm)

                    data.filter_form = request
                    data.from_date = $('#products-table-date-filter').val()

                    window.queryUrl(request)

                    window.exportFilters[tableSelector] = data
                }
            },
            order: [2, 'asc'],
            columns: [
                {
                    "orderable": false,
                    "class": "text-left",
                    "createdCell": (cell) => {
                        $(cell).addClass("d-flex pt-4")
                    },
                    "title": `<div class="custom-datatable-checkbox-container-header">
                                <div>
                                    <input id="select-all-checkboxes" type="checkbox" value="0">
                                    <label for="select-all-checkboxes"></label>
                                </div>
                              </div>`,
                    "name": "products.id",
                    "data": function (data) {
                        let editButton = `
                            <a type="button" class="table-icon-button" href="/product/${data.id}/edit">
                                <i class="picon-edit-filled icon-lg" title="Edit"></i>
                            </a>
                        `;

                        let bulkEditCheckbox = `
                            <div class="custom-datatable-checkbox-container">
                                <div>
                                    <input name="bulk-edit[${data.id}]" id="bulk-edit-${data.id}" class="custom-datatable-checkbox" type="checkbox" value="0" data-id="${data.id}">
                                    <label class="mb-0" for="bulk-edit-${data.id}"></label>
                                </div>
                            </div>
                        `;

                        return bulkEditCheckbox + editButton;
                    },
                },
                {
                    "title": "Image",
                    "name": "image",
                    "orderable": false,
                    "searchable": false,
                    "data": function (data) {
                        return `
                            <a href="#" title="{{ __('Show image') }}" data-toggle="modal" data-target="#big-image-modal" data-image="${data.image}">
                                <img src="${data.image}" class="img-thumbnail" />
                            </a>
                        `
                    }
                },
                {
                    "title": "Name",
                    "data": function (data) {
                        return `<a href="${data.link_edit}">${data.name}</a>`;
                    },
                    "name": "products.name",
                    'visible': false
                },
                {
                    "title": "On Hand",
                    "data": "quantity_on_hand",
                    "name": "products.quantity_on_hand",
                    'visible': false
                },
                {
                    "title": "SKU",
                    "data": function (data) {
                        return `<a href="${data.link_edit}">${data.sku}</a>`;
                    },
                    "name": "products.sku",
                    'visible': false

                },
                {
                    "title": "Available",
                    "data": "quantity_available",
                    "name": "products.quantity_available",
                    'visible': false

                },
                {
                    "title": "Price",
                    "data": "price",
                    "name": "products.price",
                    'visible': false

                },
                {
                    "title": "Allocated",
                    "data": "quantity_allocated",
                    "name": "products.quantity_allocated",
                    'visible': false
                },
                {
                    "title": "Backorder",
                    "data": "quantity_backordered",
                    "name": "products.quantity_backordered",
                    'visible': false
                },
                {
                    "title": "Weight",
                    "data": "weight",
                    "name": "products.weight",
                    'visible': false
                },
                {
                    "title": "Height",
                    "data": "height",
                    "name": "products.height",
                    'visible': false
                },
                {
                    "title": "Length",
                    "data": "length",
                    "name": "products.length",
                    'visible': false
                },
                {
                    "title": "Width",
                    "data": "width",
                    "name": "products.width",
                    'visible': false
                },
                {
                    "title": "Barcode",
                    "name": "products.barcode",
                    'visible': false,
                    "data": function (data) {
                        return `
                            <div class="d-flex align-items-center">
                                <span class="mr-2">${data.barcode}</span>
                                ${data.print_barcode_button}
                            </div>
                        `
                    },
                },
                {
                    "title": "Tariff Code",
                    "data": "hs_code",
                    "name": "products.hs_code",
                    'visible': false
                },
                {
                    "title": "Is Kit",
                    "data": "is_kit",
                    "name": "products.is_kit",
                    'visible': false
                },
                {
                    "title": `Value (${window.app.data.currency})`,
                    "data": "value",
                    "name": "products.value",
                    'visible': false
                },
                {
                    'title': 'Tags',
                    'data': 'tags',
                    'name': 'tags.name',
                    'visible': false,
                    "orderable": false
                },
                {
                    "title": "Date",
                    "data": "date",
                    "name": "products.created_at",
                    'visible': false
                },
                {
                    'non_hiddable': true,
                    "orderable": false,
                    "class": "text-right",
                    "title": "",
                    "name": "",
                    "data": function (data) {
                        let recoverButton = `<button title="Recover product" type="button" data-id="${data.id}" class="delete-icon recover-icon table-icon-button" data-toggle="modal" data-target="#recover-product-modal">
                                <i class="picon-history-filled icon-lg" title="Recover"></i>
                            </button>`;

                        let deleteButton = app.tableDeleteButton(
                            `<strong>Are you sure you want to delete this product?</strong><br /><br />
                            <strong>Name: </strong>${data.name}<br />
                            <strong>SKU: </strong>${data.sku}`,
                            data.link_delete,
                            true
                        );

                        if (data.is_deleted) {
                            return recoverButton;
                        } else {
                            return deleteButton;
                        }
                    }
                }
            ]
        })
    }

    $(document).ready(function () {
        const productCreateModal = $('#productCreateModal')

        $(document).on('click', '#add_item', function () {
            const dropdownContainer = $(this).closest('form').find('.supplier_container');
            const container = $(this).closest('form').find('#supplier_container');
            container.append(
                '<tr>' +
                '<td  style="white-space: unset">' +
                '<select ' +
                'name="suppliers[]" ' +
                'class="' + container.data('className') + ' getFilteredSuppliers" ' +
                'data-ajax--url="' + container.data('url') + '" ' +
                'data-placeholder="' + container.data('placeholder') + '" ' +
                'data-minimum-input-length="1" ' +
                'data-toggle="select" ' +
                '<option value=""></option> ' +
                '</select>' +
                '</td>' +
                '<td class="delete-supplier-row delete-action"><div><i class="fas fa-trash-alt text-lightGrey"></i></div></td>' +
                '</tr>'
            )

            $('.inputs-container').animate({scrollTop: $('.inputs-container').width()}, 500);

            container.find('select.getFilteredSuppliers').select2({
                dropdownParent: dropdownContainer.find("table"),
                ajax: {
                    data: function (params) {
                        return {
                            term: params.term,
                            excludedIds: window.excludedSuppliersIds ?? []
                        }
                    }
                }
            });
        });

        $(document).on('click', '.delete-row', function () {
            if ($(this).closest('tbody').find('tr').length > 1 || $(this).closest('#addLocationBlock').find('tr').length) {
                if ($(this).closest('#addLocationBlock').length && $(this).closest('#addLocationBlock').find('tbody tr').length === 1) {
                    $(this).closest('#addLocationBlock').find('table').addClass('d-none')
                    $(this).closest('#addLocationBlock').find('.noInfo').removeClass('d-none')
                }
                $(this).closest('tr').remove()
            }
        })

        let customerSelect = $('.customer_id');

        customerSelect.on('change', function () {
            let customerId = customerSelect.val();

            if (customerId) {
                $.get("/customer/" + customerId + "/dimension_units", function (data) {
                    const results = data.results

                    $('.dimensions-label').each(function () {
                        $(this).text('(' + results['dimension'] + ')')
                    })

                    $('.weight-label').each(function () {
                        $(this).text('(' + results['weight'] + ')')
                    })

                    $('.currency-label').each(function () {
                        $(this).text('(' + results['currency'] + ')')
                    })
                })
            }
        }).trigger('change');

        let locationIndex = 0;

        $(document).on('click', '#add_location_item', function () {
            const container = $(this).closest('#addLocationBlock');
            container.find('table').removeClass('d-none')
            container.find('.noInfo').addClass('d-none')
            let rowCount = container.find('table tr').length;
            if (rowCount > 1) {
                locationIndex = rowCount;
            }

            let lotsHtmlPart = '';

            if (parseInt(lotTracking) == 1) {
                lotsHtmlPart = '<td class="searchSelect">' +
                        '<select ' +
                            'name="product_lots[' + locationIndex + '][id]" ' +
                            'class="' + container.data('className') + ' ajax-user-input" ' +
                            'data-ajax--url="' + container.data('lot-url') + '" ' +
                            'data-placeholder="' + container.data('lot-placeholder') + '" ' +
                            'data-minimum-input-length="1" ' +
                            'data-toggle="select">' +
                            '<option value=""></option> ' +
                        '</select>' +
                    '</td>' +
                    '<td>&nbsp;</td>' +
                    '<td>&nbsp;</td>';
            }

            container.find('tbody').append(
                '<tr>' +
                    '<td class="searchSelect">' +
                        '<select ' +
                            'name="product_locations[' + locationIndex + '][id]" ' +
                            'class="' + container.data('className') + ' ajax-user-input" ' +
                            'data-ajax--url="' + container.data('url') + '" ' +
                            'data-placeholder="' + container.data('placeholder') + '" ' +
                            'data-minimum-input-length="1" ' +
                            'data-toggle="select">' +
                            '<option value=""></option> ' +
                        '</select>' +
                    '</td>' +
                    '<td>&nbsp;</td>' +
                    '<td>&nbsp;</td>' +
                    '<td>' +
                        '<div class="form-group input-group-merge font-sm">' +
                            '<input type="number" name="product_locations[' + locationIndex + '][quantity]" id="input-quantity" class="locationQuantity form-control font-weight-600 text-black h-auto" placeholder="Quantity" value="0">' +
                        '</div>' +
                    '</td>' +
                    lotsHtmlPart +
                    '<td class="delete-action text-right">' +
                        '<button class="delete-icon delete-row" type="button" title="Delete Location">' +
                            '<i class="picon-trash-filled" title="Delete"></i>' +
                        '</button>' +
                    '</td>' +
                '</tr>'
            )
            container.find('select.ajax-user-input').select2({dropdownParent: $(this).parent()});
        });

        let loading = false;

        $(document).on('click', '.submitTransfer', function () {
            if (loading) {
                return;
            }

            loading = true;

            let form = $(this).closest('form')
            let action = form.attr('action')
            let formData = new FormData($(form)[0]);

            $.ajax({
                type: "POST",
                url: action,
                data: formData,
                processData: false,
                contentType: false,

                error: function (error) {
                    if (error.responseJSON.errors) {
                        $.each(error.responseJSON.errors, function (key, value) {
                            toastr.error(value)
                        });
                    }
                },

                success: function (data) {
                    $('#transfer-modal').modal('hide').on('hidden.bs.modal', () => {
                        loading = false;
                    })
                    window.dtInstances['#product-locations-table'].ajax.reload()
                    toastr.success(data.message)
                }
            });
        })

        window.updateProductLocations = function (product) {
            $.ajax({
                type: "GET",
                url: '/product/' + product + '/locations',
                data: '',
                processData: false,
                contentType: false,

                error: function (error) {
                    console.error(error)
                },

                success: function (data) {
                    let container = $(document).find('#addLocationBlock')
                    let action = '';
                    container.find('tbody').empty()
                    if (data.locations.length) {
                        $.each(data.locations, function (key, location) {

                            if (parseInt(location.pivot.quantity_on_hand) > 0) {
                                action = `
                                    <button
                                        type="button"
                                        class="transfer transfer-product-from"
                                        data-toggle="modal"
                                        data-target="#transfer-modal"
                                        data-location="${location.id}"
                                    >
                                        <i class="picon-repeat-light icon-white"></i>
                                    </button>
                                `
                            } else {
                                action = `
                                    <button class="table-icon-button delete-row" type="button" title="Delete Location">
                                        <i class="picon-trash-filled" title="Delete"></i>
                                    </button>
                                `
                            }

                            let lotsPart = ''

                            if (parseInt(lotTracking) == 1) {
                                if (location.lot_items.length > 0) {
                                    lotsPart = '<td class="searchSelect">'+
                                                '<select ' +
                                                    'name="product_lots[' + key + '][id]" ' +
                                                    'class="ajax-user-input product-location-select" ' +
                                                    'data-ajax--url="' + container.data('lot-url') + '" ' +
                                                    'data-placeholder="' + container.data('lot-placeholder') + '" ' +
                                                    'data-minimum-input-length="1" ' +
                                                    'data-toggle="select">' +
                                                    '<option value="' + location.lot_items[0]['lot'].id +'">' + location.lot_items[0]['lot'].name +'</option> ' +
                                                '</select>' +
                                            '</td>' +
                                            '<td class="' + lotTdsClasses + '">' +
                                                location.lot_items[0]['lot'].expiration_date +
                                            '</td>' +
                                            '<td class="' + lotTdsClasses + '">' +
                                                location.lot_items[0]['lot'].supplier.contact_information.name +
                                            '</td>';
                                } else {
                                    lotsPart = '<td>&nbsp;</td><td class=" + lotTdsClasses + ">&nbsp;</td><td>&nbsp;</td>';
                                }
                            }

                            let locationRow =
                                '<tr>' +
                                    '<td class="searchSelect">' +
                                        '<select ' +
                                            'name="product_locations[' + key + '][id]" ' +
                                            'class="ajax-user-input"' +
                                            'data-ajax--url="' + container.data('url') + '" ' +
                                            'data-placeholder="' + container.data('placeholder') + '" ' +
                                            'data-minimum-input-length="1" ' +
                                            'data-toggle="select">' +
                                                '<option value="' + location.id + '" selected="selected">' + location.name + '</option> ' +
                                        '</select>' +
                                    '</td>' +
                                    '<td>' +
                                        (parseInt(location.pickable) == 1 ? 'YES' + (parseInt(location.pickable_effective) == 0 ? ' effective NO' : '') : 'NO') +
                                    '</td>' +
                                    '<td>' + (parseInt(location.sellable) == 1 ? 'YES' + (parseInt(location.sellable_effective) == 0 ? ' effective NO' : '') : 'NO') + '</td>' +
                                    '<td>' +
                                        '<div class="flex-grow-1 form-group mb-0 text-left d-flex flex-column justify-content-end">' +
                                        '<div class="input-group input-group-alternative input-group-merge">' +
                                        '<input type="number" name="product_locations[' + key + '][quantity]" id="input-quantity" class="locationQuantity form-control font-weight-600 text-black h-auto" placeholder="Quantity" value="' + location.pivot.quantity_on_hand + '">' +
                                        '</div>' +
                                        '</div>' +
                                    '</td>' +
                                    lotsPart +
                                    '<td class="delete-action">' + action + '</td>' +
                                '</tr>'

                            container.find('tbody').append(locationRow)
                        })
                        container.find('table').removeClass('d-none')
                        container.find('.noInfo').addClass('d-none')
                        $(document).find('select.ajax-user-input').select2({
                            dropdownParent: container
                        });
                    } else {
                        container.find('table').addClass('d-none')
                        container.find('.noInfo').removeClass('d-none')
                    }
                }
            });
        }

        $(document).on('click', '.remove-location-for-product', function () {
            const location = $(this).data('location');
            const product = $(this).data('product');
            let _this = $(this);

            $.post("/product/removeFromLocation/" + product,
                {
                    "_token": $('#token').val(),
                    'location_id': location
                },
                function () {
                    _this.parent().parent().remove();
                    toastr.success('Location successfully removed')
                });
        });

        $(document).on('click', '.transfer-product-from', function () {
            let location = $(this).data('location');
            $("#from_location_id").val(location);

            let url = $("[name='to_location_id']").data('ajax--url')
            url = url.replace(/[?](from_location_id=)\d+/g, '')
            url += '?from_location_id=' + location

            $("[name='to_location_id']").data('ajax--url', url)

            $("[name='to_location_id']").select2({
                dropdownParent: $('#transfer-modal')
            });
        });

        $(document).on('click', '#add-new-location', function () {

            addLocationRowInTable();
            $('#add-new-location-modal').modal('show');
        });

        $(document).on('click', '#add-another-location', function () {
            addLocationRowInTable();
        });

        $(document).on('click', '#add-to-location-form-save', function () {

            let formVars = $('#add-to-location-form').serializeArray();

            formVars.map(function(formVar){

                if (formVar.value != '' && formVar.value != '0') {
                    $('<input>').attr({
                        type: 'hidden',
                        value: formVar.value,
                        name: formVar.name
                    }).appendTo('#locations-form');
                }
            });

            $('#add-new-location-modal').modal('hide');
            $('#add-new-location-table').find('tbody').html('');
            $(".globalSave").click();
        });

        $(document).on('click', '#add-to-location-form-cancel', function () {
            $('#add-new-location-modal').modal('hide');
            $('#add-new-location-table').find('tbody').html('');
        });

        $(document).on('click', '#delete-empty-locations', function () {
            $(".locationQuantity").each(function() {

                if (parseInt($(this).val()) == 0) {
                    $(this).closest('tr').remove();
                }
            });

            $(".globalSave").click();
        });


        function addLocationRowInTable() {

            const container = $('#add-new-location-table');
            let locationIndex = 0;
            let rowCount = parseInt($('#add-new-location-table tbody tr').length);
            let rowCountInView = parseInt($('#product-locations-table tbody tr').length);
            let totalCount = rowCount + rowCountInView;

            if (totalCount > 0) {
                locationIndex = totalCount;
            }

            let lotsHtmlPart = '';

            if (parseInt(lotTracking) == 1) {
                lotsHtmlPart = '<td class="searchSelect">' +
                    '<select ' +
                    'name="product_lots[' + locationIndex + '][id]" ' +
                    'class="' + container.data('className') + ' ajax-user-input" ' +
                    'data-ajax--url="' + container.data('lot-url') + '" ' +
                    'data-placeholder="' + container.data('lot-placeholder') + '" ' +
                    'data-minimum-input-length="1" ' +
                    'data-toggle="select">' +
                    '<option value=""></option> ' +
                    '</select>' +
                    '</td>';
            }

            container.find('tbody').append(
                '<tr>' +
                    '<td class="searchSelect">' +
                        '<select ' +
                        'name="product_locations[' + locationIndex + '][id]" ' +
                        'class="' + container.data('className') + ' ajax-user-input" ' +
                        'data-ajax--url="' + container.data('url') + '" ' +
                        'data-placeholder="' + container.data('placeholder') + '" ' +
                        'data-minimum-input-length="1" ' +
                        'data-toggle="select">' +
                        '<option value=""></option> ' +
                        '</select>' +
                    '</td>' +
                    '<td>' +
                        '<div class="form-group input-group-merge m-0">' +
                            '<input type="number" name="product_locations[' + locationIndex + '][quantity]" id="input-quantity" class="locationQuantity number-input form-control font-weight-600 text-black h-auto" placeholder="Quantity" value="0">' +
                        '</div>' +
                    '</td>' +
                    lotsHtmlPart +
                    '<td class="delete-action text-right">' +
                        '<button class="delete-icon delete-row" type="button" title="Delete Location">' +
                            '<i class="picon-trash-filled" title="Delete"></i>' +
                        '</button>' +
                    '</td>' +
                '</tr>'
            )
            container.find('select.ajax-user-input').select2({dropdownParent: $(this).parent()});

            $(".ajax-user-input").select2({
                dropdownParent: $('#add-new-location-modal')
            });
        }

        // TODO Modals
        function openCreationModal() {
            let hash = window.location.hash;

            if (hash && hash === '#open-modal') {
                $(document).find('#productCreateModal').modal('show')
                window.location.hash = '';
            }

            const productCreateModal = $("#productCreateModal")

            productCreateModal.find('.customer_id').select2({
                dropdownParent: productCreateModal
            })

            productCreateModal.find('.select-tag').select2({
                dropdownParent: productCreateModal
            })
        }

        openCreationModal();

        $(document).on('click', '.openCreateModal', function () {
            openCreationModal();
        })

        $(window).on('hashchange', function (e) {
            openCreationModal();
        });

        $('select.send-filtered-request').select2({
            dropdownParent: $("#rows_container table"),
            ajax: {
                transport: function (args, success, failure) {
                    if (customerSelect.val() !== null) {
                        args.url = `${args.url}/${customerSelect.val()}`
                    }
                    let $request = $.ajax(args);
                    $request.then(success);
                    $request.fail(failure);
                    return $request;
                },
                data: function (params) {
                    return {
                        term: params.term,
                        excludedIds: excludedIds
                    }
                }
            }
        });

        $(document).find('select.getFilteredSuppliers').select2({
            dropdownParent: ($('.editContainer').length ? $(document).find(".supplier_container table") : productCreateModal.find(".supplier_container table")),
            ajax: {
                data: function (params) {
                    return {
                        term: params.term,
                        excludedIds: window.excludedSuppliersIds ?? []
                    }
                }
            }
        });

        $(document).on('select2:select', 'select.send-filtered-request', function (e) {
            excludedIds.push(e.params.data.id)
        });

        $(document).on('select2:select', 'select.getFilteredSuppliers', function (e) {
            let suppliersPreview = $(document).find('.suppliersPreview')
            let rowData = e.params.data.text.split(',')
            let rowItems = ''

            rowData.map(function (value) {
                rowItems += '<td>' + value + '</td>'
            })

            if (suppliersPreview.length) {
                suppliersPreview.find('.empty').addClass('d-none')
                suppliersPreview.find('.table').removeClass('d-none')
                suppliersPreview.find('table tbody').append('<tr data-id="' + e.params.data.id + '">' + rowItems + '</tr>')
            }
            excludedSuppliersIds.push(e.params.data.id)
        });

        $(document).on('change', '.type-select', function (e) {
            let container = $(this).closest('form').find('#rows_container');
            let heading = $(this).closest('form').find('.headingSection');

            if ($(this).val() === '1' || $(this).val() === '2') {
                container.find('select').prop('disabled', false);
                container.find('input').prop('disabled', false);
                container.removeClass('d-none')
                heading.removeClass('d-none')
                $('.inputs-container').animate({scrollTop: $('.inputs-container').width()}, 500);
            } else {
                container.find('select').prop('disabled', 'disabled');
                container.find('input').prop('disabled', 'disabled');
                container.addClass('d-none')
                heading.addClass('d-none')
            }
        });

        $(document).on('click', '#add_row', function () {
            const container = $(this).closest('#rows_container');
            let index = 0;

            if (container.find('tr').length) {
                index = isNaN(container.find('tr').last().data('index')) ? 0 : parseInt(container.find('tr').last().data('index')) + 1
            }

            container.find('table').append(
                '<tr data-index="' + index + '">' +
                '<td>' +
                '<div class="input-group input-group-alternative input-group-merge text-left">' +
                '<select ' +
                'name="kit_items[' + index + '][id]" ' +
                'class="' + container.data('classname') + '" ' +
                'data-ajax--url="' + container.data('url') + '" ' +
                'data-placeholder="' + container.data('placeholder') + '" ' +
                'data-minimum-input-length="1" ' +
                'data-toggle="select" ' +
                '<option value=""></option> ' +
                '</select>' +
                '</div>' +
                '</td>' +
                '<td>' +
                '<div class="input-group input-group-alternative input-group-merge">' +
                '<input type="number" name="kit_items[' + index + '][quantity]" class="form-control font-sm bg-white font-weight-600 text-neutral-gray h-auto p-2">' +
                '</div>' +
                '</td>' +
                '<td class="delete-row"><div><i class="fas fa-trash-alt text-lightGrey"></i></div></td>' +
                '</tr>'
            )

            container.find('select').select2({
                dropdownParent: container.find('table'),
                ajax: {
                    transport: function (args, success, failure) {
                        if (customerSelect.val() !== null) {
                            args.url = `${args.url}/${customerSelect.val()}`
                        }
                        let $request = $.ajax(args);
                        $request.then(success);
                        $request.fail(failure);
                        return $request;
                    },
                    data: function (params) {
                        return {
                            term: params.term,
                            excludedIds: excludedIds
                        }
                    }
                }
            })
        });

        $(document).on('click', '.delete-supplier-row', function () {
            let removeItem = parseInt($(this).closest('tr').find('select').val());
            let suppliersPreview = $(document).find('.suppliersPreview')

            if (suppliersPreview.length) {
                $(document).find('.suppliersPreview').find('tr[data-id=' + removeItem + ']').remove()
                if (!$(document).find('.suppliersPreview').find('tbody tr').length) {

                    suppliersPreview.find('.empty').removeClass('d-none')
                    suppliersPreview.find('.table').addClass('d-none')
                }
            }

            if (removeItem && !isNaN(removeItem)) {
                excludedIds = jQuery.grep(window.excludedIds ?? [], function (value) {
                    return value !== removeItem;
                });
                excludedSuppliersIds = jQuery.grep(window.excludedSuppliersIds ?? [], function (value) {
                    return value !== removeItem;
                });
            }
            $(this).closest('tr').remove()
        })

        window.ckeditor();

        $(document).on('click', '.product_details_edit', function () {
            $('.product-details-checkboxes-title').addClass('d-none')
            $('.priority-counting-checkbox').removeClass('d-none')
            $('.serial-number-checkbox').removeClass('d-none')
        });

        $(document).on('click', '.productForm .saveButton:not(#submit-button)', function (e) {
            e.preventDefault();

            $(this).addClass('d-none')
            $(document).find('.form-error-messages').remove()

            let _form = $(this).closest('.productForm');

            _form.removeClass('editable')
            _form.find('.loading').removeClass('d-none')

            let form = _form[0];
            let formData = new FormData(form);

            if (_form.find('[name="tags[]"]').length && !formData.has('tags[]')) {
                formData.append('tags[]', '')
            }

            $.ajax({
                type: 'POST',
                url: _form.attr('action'),
                enctype: 'multipart/form-data',
                headers: { 'X-CSRF-TOKEN': formData.get('_token') },
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    _form.find('.loading').addClass('d-none')
                    _form.find('.saveSuccess').removeClass('d-none').css('display', 'block').fadeOut(5000)
                    _form.find('.notes-data span').html(data.product.notes)
                    _form.find('.product-details-checkboxes-title').removeClass('d-none')
                    _form.find('.priority-counting-checkbox').addClass('d-none')
                    _form.find('.serial-number-checkbox').addClass('d-none')
                    _form.find('#edit-kit-items').addClass('d-none')
                    _form.find('#product-kits-table-container').removeClass('d-none')

                    if (data.product['kit_type'] == 0) {
                        $('#kits-form').addClass('d-none')
                        $('#locations-form').removeClass('d-none')
                        $('#kit-items-table tr').slice(1).remove()
                    } else if (data.product['kit_type'] == 1) {
                        $('#kits-form').removeClass('d-none')
                        $('#locations-form').addClass('d-none')
                    }
                    window.dtInstances['#product-kits-table'].ajax.reload()
                    $.each($('#kit-items-table tr'), function (index, value) {
                        if ($(value).find('select').val() == null || $(value).find("td:eq(1) input").val() == '') {
                            $(value).remove();
                        }
                    })

                    if (data.product.priority_counting_requested_at != null) {
                        _form.find('.priority-counting-status').html('Yes')
                    } else {
                        _form.find('.priority-counting-status').html('No')
                    }

                    if (data.product.has_serial_number == "1") {
                        _form.find('.serial-number-status').html('Yes')
                    } else {
                        _form.find('.serial-number-status').html('No')
                    }

                    if (data.product.lot_tracking == "1") {
                        _form.find('.lot-tracking-status').html('Yes')
                    } else {
                        _form.find('.lot-tracking-status').html('No')
                    }

                    reloadAuditLog()
                    toastr.success(data.message)
                    window.dtInstances['#product-locations-table'].ajax.reload()
                },
                error: function (messages) {
                    _form.find('.loading').addClass('d-none')
                    _form.find('.saveError').removeClass('d-none').removeClass('d-none').css('display', 'block').fadeOut(5000)
                    _form.addClass('editable')

                    if (messages.responseJSON.errors) {
                        $.each(messages.responseJSON.errors, function (key, value) {
                            toastr.error(value)
                        });
                    }

                    $.map(messages.responseJSON.errors, function (value, key) {
                        let label = _form.find('label[data-id="' + key + '"]')
                            .append('<span class="validate-error text-danger form-error-messages">&nbsp;&nbsp;&nbsp;&nbsp;' + value[0] + '</span>')

                        let error_type = key.split('.')

                        if (error_type && error_type.length && error_type[0] === 'kit_items') {
                            $(document).find('.validation_errors').append('<span class="validate-error text-danger form-error-messages">' + value[0] + '</span><br>')
                        }

                        let hasError = label.closest('.tab-pane').attr('id');
                        $(document).find('a[href="#' + hasError + '"]').addClass('text-danger')
                    })

                    let hasErrorTab = $(document).find('.validate-error').closest('.tab-pane').attr('id');

                    $(document).find('a[href="#' + hasErrorTab + '"]').first().trigger('click')
                }
            });
        })

        $(document).on('click', '#save-added-kit-items', function () {
            const container = $(this).closest('#addKitItems');

            $('#product-kits-table_wrapper tbody').append(container.find('tbody tr'))
            container.modal('hide');
        })

        //Import and export modals
        $('.import-products').click(function (e) {
            e.preventDefault();
            e.stopPropagation();

            let _form = $(this).closest('.import-products-form');
            let form = _form[0];
            let formData = new FormData(form);

            $.ajax({
                type: 'POST',
                url: _form.attr('action'),
                headers: { 'X-CSRF-TOKEN': formData.get('_token') },
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    $('#csv-filename').empty()

                    toastr.success(data.message)

                    window.dtInstances['#products-table'].ajax.reload()
                },
                error: function (response) {
                    if (response.status != 504) {
                        let modal = $('#import-products-modal');

                        $('#csv-filename').empty()

                        toastr.error('Invalid CSV data')
                        appendValidationMessages(modal, response)
                    }
                }
            });

            $('#import-products-modal').modal('hide');
            toastr.info('Product import started. You may continue using the system');
        });

        $('.import-kit-items').click(function (e) {
            e.preventDefault();
            e.stopPropagation();

            let _form = $(this).closest('.import-kit-items-form');
            let form = _form[0];
            let formData = new FormData(form);

            $.ajax({
                type: 'POST',
                url: '/product/import/csv/' + product,
                headers: { 'X-CSRF-TOKEN': formData.get('_token') },
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    $('#import-kit-items-modal').modal('toggle');

                    $('#csv-filename').empty()

                    toastr.success(data.message)

                    window.dtInstances['#product-kits-table'].ajax.reload()
                },
                error: function (response) {
                    let modal = $('#import-kit-items-modal');

                    $('#csv-filename').empty()

                    toastr.error('Invalid CSV data')
                    appendValidationMessages(modal, response)
                }
            });
        });

        $('.export-products').click(function () {
            $('#export-products-modal').modal('toggle');
        });

        $('.export-kit-items').click(function () {
            $('#export-kit-items-modal').modal('toggle');
        });

        $('#big-image-modal').on('hidden.bs.modal', function () {
            $('#big-image-url').attr('src', '/img/loading.gif')
            $('#big-image-url').attr('width', '50px')
        })

        $('#big-image-modal').on('shown.bs.modal', function (e) {
            $('#big-image-url').removeAttr('width')
            $('#big-image-url').attr('src', $(e.relatedTarget).data('image'))
        })

        $('#products-csv-button').on('change', function (e) {
            if (e.target.files) {
                if (e.target.files[0]) {
                    let filename = e.target.files[0].name
                    $('#csv-filename').append(
                        '<h5 class="heading-small">' +
                        'Filename: ' + filename +
                        '</h5>'
                    )
                }

                $('#import-products-modal').focus()
            }
        })

        $('#kit-items-csv-button').on('change', function (e) {
            if (e.target.files) {
                if (e.target.files[0]) {
                    let filename = e.target.files[0].name
                    $('#csv-filename').append(
                        '<h5 class="heading-small">' +
                        'Filename: ' + filename +
                        '</h5>'
                    )
                }

                $('#import-kit-items-modal').focus()
            }
        })

        $('#recover-product-modal').on('show.bs.modal', function (event) {
            let button = $(event.relatedTarget)
            let actionId = button.data('id')

            $(".recover-product-form").attr('action', '/product/' + actionId + '/recover');
        })

        $('.recover-product').click(function (e) {
            e.preventDefault();
            e.stopPropagation();

            let _form = $(this).closest('.recover-product-form');
            let form = _form[0];
            let formData = new FormData(form);

            $.ajax({
                type: 'POST',
                url: form.action,
                headers: { 'X-CSRF-TOKEN': formData.get('_token') },
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    $('#recover-product-modal').modal('toggle')

                    toastr.success(data.message)

                    window.dtInstances['#products-table'].ajax.reload()
                }
            });
        });

        $('#bulk-edit-modal').on('show.bs.modal', function () {
            let ids = []
            let form = $('#bulk-edit-form')
            $('#item-type').text('Products')


            $('input[name^="bulk-edit"]').each(function() {
                if ($(this).prop('checked')) {
                    let productId = $(this).data('id')

                    ids.push(parseInt(productId))
                }
            })

            $('#number-of-selected-items').text(ids.length)
            $('#model-ids').val(ids)

            $.ajax({
                type: 'POST',
                serverSide: true,
                url: '/product/bulkSelectionStatus',
                data: {
                    'ids': ids
                },
                success: function(data) {
                    const res = data.results

                    if (res.deleted) {
                        $('#products-bulk-delete').attr('hidden', true)
                        $('#products-bulk-recover').removeAttr('hidden')
                    } else if (res.existing) {
                        $('#products-bulk-delete').removeAttr('hidden')
                        $('#products-bulk-recover').attr('hidden', true)
                    } else {
                        $('#products-bulk-delete').attr('hidden', true)
                        $('#products-bulk-recover').attr('hidden', true)
                    }
                }
            })

            form.attr('action', '/product/bulk-edit')
            form.serialize()
        })

        $('#' + tableSelector + '-table').on('packiyo:ajax-success', 'form.ajax-form', function () {
            window.dtInstances['#' + tableSelector + '-table'].ajax.reload();
        });
    })

    $('#submit-bulk-product-edit').click(function (e) {
        e.preventDefault()
        e.stopPropagation()

        let modal = $('#bulk-edit-modal')
        let form = $('#bulk-edit-form')

        let formData = new FormData(form[0])

        $.ajax({
            type: 'POST',
            url: form[0].action,
            headers: {'X-CSRF-TOKEN': formData.get('_token')},
            data: formData,
            processData: false,
            contentType: false,
            success: function () {
                modal.modal('toggle')

                $('#bulk-edit-btn').attr('hidden', true)

                $('#select-all-checkboxes').prop('checked', false)

                $('.bulk-edit-tags').empty()

                form[0].reset()

                toastr.success('Updated successfully!')

                window.dtInstances['#products-table'].ajax.reload()
            },
            error: function (response) {
                appendValidationMessages(modal, response)
            }
        })
    })


    $('#products-table').on('draw.dt', function() {
        const selectAllCheckboxes = $('#select-all-checkboxes')
        const datatableCheckboxes = $('.custom-datatable-checkbox')

        if (selectAllCheckboxes.prop('checked')) {
            datatableCheckboxes.each(function (i, element) {
                element.checked = true
            })
        } else {
            datatableCheckboxes.each(function (i, element) {
                element.checked = false
            })
        }
    })

    $('#products-bulk-delete').on('click', function () {
        let _form = $(this).closest('#bulk-edit-form');
        let form = _form[0];
        let formData = new FormData(form);

        app.confirm('Bulk delete products', 'Are you sure you want to delete these products?', () => {
            $.ajax({
                type: 'POST',
                url: '/product/bulk-delete',
                enctype: 'multipart/form-data',
                headers: {'X-CSRF-TOKEN': formData.get('_token')},
                data: formData,
                processData: false,
                contentType: false,
                success: function () {
                    $('#bulk-edit-modal').modal('toggle')

                    toastr.success('Products were deleted successfully')

                    window.dtInstances['#products-table'].ajax.reload()
                },
                error: function () {
                    toastr.error('Unable to cancel products')
                }
            })
        })
    })

    $('#products-bulk-recover').on('click', function () {
        let _form = $(this).closest('#bulk-edit-form');
        let form = _form[0];
        let formData = new FormData(form);

        app.confirm('Recover deleted products', 'Are you sure you want to recover these deleted products?', () => {
            $.ajax({
                type: 'POST',
                url: '/product/bulk-recover',
                enctype: 'multipart/form-data',
                headers: {'X-CSRF-TOKEN': formData.get('_token')},
                data: formData,
                processData: false,
                contentType: false,
                success: function () {
                    $('#bulk-edit-modal').modal('toggle')

                    toastr.success('Products were recovered successfully')

                    window.dtInstances['#products-table'].ajax.reload()
                },
                error: function () {
                    toastr.error('Unable to recover the selected products')
                }
            })
        })
    })

    $('#productKitDelete').on('show.bs.modal', function (e) {
        $('#productKitDelete .modal-content').html(`<div class="spinner">
                <img src="../../img/loading.gif">
            </div>`)
        let itemId = $(e.relatedTarget).data('id');

        $.ajax({
            type: 'GET',
            serverSide: true,
            url: '/product/deleteKitProduct/' + itemId + '?parentId=' + parentId,

            success: function (data) {

                $('#productKitDelete>div').html(data);
            },
        });
    })

    if ($('#product-locations-table').length && product) {

        const container = $('#addLocationBlock');

        let lotNameCol = {
            "title": "Lot Name",
            "name": "lot_name",
            "data": function (data, type, full, meta){
                return '<div class="form-group" readonly>' +
                    '<select ' +
                    'name="product_lots[' + meta.row + '][id]" ' +
                    'class="ajax-user-input product-location-select" ' +
                    'data-ajax--url="' + container.data('lot-url') + '" ' +
                    'data-placeholder="' + container.data('lot-placeholder') + '" ' +
                    'data-minimum-input-length="1" ' +
                    'data-toggle="select">' +
                    '<option value="' + data.lot_id +'">' + data.lot_name +'</option> ' +
                    '</select>' +
                    '</div>';
            },
            "className": lotTdsClasses,
        };

        let lotExpCol = {
            "title": "Expiration",
            "name": "lot_expiration",
            "data": function (data){
                return '<div class="' + lotTdsDivClasses + '">' + data.lot_expiration + '</div>';
            },
            "className": lotTdsClasses,
        };

        let lotVendorCol = {
            "title": "Vendor",
            "name": "lot_vendor",
            "data": function (data){
                return '<div class="' + lotTdsDivClasses + '">' + data.lot_vendor + '</div>';
            },
            "className": lotTdsClasses,
        };

        let actionsCol = {
            "title": '',
            "data" : function(data){
                if (data.quantity > 0) {
                    return '<button type="button" class="transfer transfer-product-from float-right" data-toggle="modal" data-target="#transfer-modal" data-location="' + data.id + '">' +
                        '<i class="picon-repeat-light icon-lg"></i>' +
                        '</button>';
                } else {
                    return '<button class="table-icon-button delete-row float-right" type="button" title="Delete Location">' +
                        '<i class="picon-trash-filled"></i>' +
                        '</button>';
                }
            },
            "orderable": false
        };

        let columnsArray = [
            {
                "title": "",
                "data": 'id',
                "name": "id",
                "visible": false
            },
            {
                "title": "Location",
                "name": "name",
                "data": function (data, type, full, meta){
                    return '<div class="form-group" readonly>' +
                        '<select ' +
                        'name="product_locations[' + meta.row + '][id]" ' +
                        'class="ajax-user-input product-location-select" ' +
                        'data-ajax--url="' + container.data('url') + '" ' +
                        'data-placeholder="' + container.data('placeholder') + '" ' +
                        'data-minimum-input-length="1" ' +
                        'data-toggle="select">' +
                        '<option value="' + data.id +'">' + data.name +'</option> ' +
                        '</select>' +
                        '</div>';
                },
            },
            {
                "title": "Pickable",
                "name": "location_pickable",
                "data": function (data){
                    return '<div class="' + lotTdsDivClasses + '">' + data.location_pickable + '</div>';
                },
                "className": lotTdsClasses,
                "width": "5%"
            },
            {
                "title": "Sellable",
                "name": "location_sellable",
                "data": function (data){
                    return '<div class="' + lotTdsDivClasses + '">' + data.location_sellable + '</div>';
                },
                "className": lotTdsClasses,
                "width": "5%"
            },
            {
                "title": "Quantity",
                "name": "quantity",
                "data": function (data, type, full, meta){
                    return '<div class="form-group input-group-merge font-sm">' +
                        '<input type="number" name="product_locations[' + meta.row + '][quantity]" ' +
                        'id="input-product_locations[' + meta.row + '][quantity]" ' +
                        'class="locationQuantity number-input form-control font-weight-600 text-black h-auto" ' +
                        'placeholder="" ' +
                        'value="' + data.quantity +'"' +
                        '/>' +
                        '</div>';
                },
                "className": lotTdsClasses,
                "width": "7%"
            }
        ];

        if (parseInt(lotTracking) == 1) {
            columnsArray.push(lotNameCol);
            columnsArray.push(lotExpCol);
            columnsArray.push(lotVendorCol);
        }

        columnsArray.push(actionsCol);

        window.datatables.push({
            selector: '#product-locations-table',
            resource: 'product_locations',
            ajax: {
                url: '/product/locations/' + product,
                data: function (data) {
                    data.from_date = $('#product-locations-table-date-filter').val();
                }
            },
            order: [1, 'desc'],
            aaSorting: [],
            columns: columnsArray,
            createdRow: function (row, data, dataIndex) {
                //$('td:eq(2)', row).css('min-width', '200px');
                //$('td:eq(3)', row).css('max-width', '100px');
            },
            drawCallbackOverride: function (row, data) {
                $('.product-location-select').select2({
                    dropdownParent: $("#addLocationBlock")
                });
            }
        });

        setTimeout(function(){
            $('.product-location-select').select2({
                dropdownParent: $("#addLocationBlock")
            });
        }, 1500);
    }

    if ($('#product-shipped-items-table').length && product) {
        window.datatables.push({
            selector: '#product-shipped-items-table',
            resource: 'package_order_items',
            ajax: {
                url: '/product/shipped-items-data-table/' + product,
                data: function (data) {
                    data.from_date = $('#product-shipped-items-table-date-filter').val();
                }
            },
            order: [2, 'desc'],
            aaSorting: [],
            columns: [
                {
                    "title": "",
                    "data": 'id',
                    "name": "id",
                    "visible": false
                },
                {
                    "title": "Order Number",
                    "name": "order_number",
                    "data": function (data) {
                        return `
                            <a href="/order/${data.order_id}/edit" target="_blank">
                                ${data.order_number}
                            </a>
                        `;
                    },
                    "visible": false
                },
                {
                    "title": "Quantity",
                    "data": 'quantity',
                    "name": "quantity",
                    "visible": false
                },
                {
                    "title": "Location Name",
                    "data": function (data) {
                        return `
                            <a href="/location/${data.location_id}/edit" target="_blank">
                                ${data.location_name}
                            </a>
                        `;
                    },
                    "name": "location_name",
                    "visible": false
                },
                {
                    "title": "Lot Name",
                    "data": "lot_name",
                    "name": "lot_name",
                    "visible": false
                },
                {
                    "title": "Lot Expiration",
                    "data": "lot_expiration",
                    "name": "lot_expiration",
                    "visible": false
                },
                {
                    "title": "Vendor",
                    "data": "vendor",
                    "name": "vendor",
                    "visible": false
                },
                {
                    "title": "Tote Name",
                    "data": "tote_name",
                    "name": "tote_name",
                    "visible": false
                },
                {
                    "title": "Serial Number",
                    "data": "serial_number",
                    "name": "serial_number",
                    "visible": false
                }
            ],
            createdRow: function (row, data, dataIndex) {
                $('td:eq(2)', row).css('min-width', '200px');
            }
        });
    }


    if ($('#product-order-items-table').length && product) {
        window.datatables.push({
            selector: '#product-order-items-table',
            resource: 'product-order-items',
            ajax: {
                url: '/product/order-items-data-table/' + product,
                data: function (data) {
                    data.from_date = $('#product-order-items-table-date-filter').val();
                }
            },
            aaSorting: [],
            columns: [
                {
                    "class": "text-left",
                    "title": "",
                    "name": "order_items.id",
                    "data": function (data) {
                        return ''
                    },
                    "visible": false

                },
                {
                    "title": "Order Date",
                    "data": "ordered_at",
                    "name": "ordered_at",
                    "visible": false
                },
                {
                    "title": "Order Number",
                    "name": "orders.number",
                    "data": function (data) {
                        return `
                            <a href="/order/${data.order_id}/edit" target="_blank">
                                ${data.order_number}
                            </a>
                        `;
                    },
                    "visible": false,
                    'orderable': false
                },
                {
                    "title": "Quantity",
                    "data": 'quantity',
                    "name": "quantity",
                    "visible": false
                },
                {
                    "title": "Qty Pending",
                    "data": "quantity_pending",
                    "name": "quantity_pending",
                    "visible": false
                },
                {
                    "title": "Qty Shipped",
                    "data": "quantity_shipped",
                    "name": "quantity_shipped",
                    "visible": false
                },
                {
                    "title": "Qty Allocated",
                    "data": "quantity_allocated",
                    "name": "quantity_allocated",
                    "visible": false
                },
                {
                    "title": "Qty Backorder",
                    "data": "quantity_backordered",
                    "name": "quantity_backordered",
                    "visible": false
                },
            ],
            createdRow: function (row, data, dataIndex) {
                $('td:eq(2)', row).css('min-width', '200px');
            }
        });
    }

    if ($('#totes-item-table').length && product) {
        window.datatables.push({
            selector: '#totes-item-table',
            resource: 'tote_order_items',
            ajax: {
                url: '/product/tote-items-data-table/' + product,
                data: function (data) {
                    data.from_date = $('#totes-item-table-date-filter').val();
                }
            },
            order: [1, 'desc'],
            columns: [
                {
                    "title": "Order number",
                    "data": function (data) {
                        return `<a href="${data.order.url}">${data.order.number}</a>`;
                    },
                    "name": "orders.number"
                },
                {
                    "title": "Tote name",
                    "data": function (data) {
                        return `<a href="${data.tote.url}">${data.tote.name}</a>`;
                    },
                    "name": "totes.name",
                    "visible": false
                },
                {
                    "title": "Quantity added",
                    "data": "quantity",
                    "name": "quantity",
                    "class": "text-neutral-text-gray",
                    "visible": false
                },
                {
                    "title": "Quantity removed",
                    "data": "quantity_removed",
                    "name": "quantity_removed",
                    "class": "text-neutral-text-gray",
                    "visible": false
                },
                {
                    "title": "Updated date",
                    "data": "updated_at",
                    "name": "tote_order_items.updated_at",
                    "class": "text-neutral-text-gray",
                    "visible": false
                }
            ]
        });
    }

    if ($('#product-kits-table').length && product) {
        const filterForm = $('#toggleFilterForm').find('form')

        window.datatables.push({
            selector: '#product-kits-table',
            resource: 'kits',
            ajax: {
                url: '/product/kits-data-table/' + product,
                data: function (data) {
                    let request = {}
                    filterForm
                        .serializeArray()
                        .map(function (input) {
                            request[input.name] = input.value;
                        });
                    data.filter_form = request
                    data.from_date = $('#product-kits-table-date-filter').val();
                }
            },
            aaSorting: [],
            order: [1, 'asc'],
            columns: [
                {
                    "orderable": false,
                    "class": "text-left ",
                    "title": "Action",
                    "name": "products.id",
                    "data": function (data) {
                        if (data.is_deleted) {
                            return (
                                '<button title="Recover product" type="button" data-id="' +
                                data.id +
                                '" class="delete-icon recover-icon" data-toggle="modal" data-target="#recover-product-modal">' +
                                '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                                '<path d="M20 12C20 7.58172 16.4183 4 12 4C9.48295 4 7.27119 5.1635 5.81776 6.99321L7.49707 6.98829C8.04935 6.98667 8.49838 7.43307 8.5 7.98535C8.50161 8.53763 8.05521 8.98666 7.50293 8.98828L4.06007 8.99836C4.02166 9.00071 3.983 9.00084 3.94428 8.9987L3.50293 9C3.23721 9.00077 2.9821 8.89576 2.79393 8.70814C2.60576 8.52052 2.5 8.26572 2.5 8L2.5 4C2.5 3.44771 2.94772 3 3.5 3C4.05229 3 4.5 3.44772 4.5 4L4.5 5.44947C6.31255 3.34038 8.97702 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C8.06405 22 4.73586 19.7278 3.10449 16.445C2.8587 15.9505 3.06039 15.3503 3.55497 15.1045C4.04955 14.8587 4.64973 15.0604 4.89551 15.555C6.20955 18.1991 8.86851 20 12 20C16.4183 20 20 16.4183 20 12Z" fill="#6F767E"/>' +
                                '<path fill-rule="evenodd" clip-rule="evenodd" d="M12 7C12.5523 7 13 7.44772 13 8V11.5858L14.7071 13.2929C15.0976 13.6834 15.0976 14.3166 14.7071 14.7071C14.3166 15.0976 13.6834 15.0976 13.2929 14.7071L11.5858 13C11.2107 12.6249 11 12.1162 11 11.5858V8C11 7.44772 11.4477 7 12 7Z" fill="#6F767E"/>' +
                                "</svg>" +
                                "</button>"
                            );
                        }

                        return `
                            <button type="button" class="table-icon-button" data-id="${data.id}" data-toggle="modal" data-target="#productKitDelete">
                                <i class="picon-trash-filled delete-icon" title="Delete"></i>
                            </button>
                        `
                    },
                },
                {
                    "title": "Name",
                    // "data": "name",
                    "data": function (data) {
                        return `
                            <a href="/product/${data.id}/edit" target="_blank">
                                ${data.name}
                            </a>
                        `;
                    },
                    "name": "products.name",
                    "visible": false,
                },
                {
                    "title": "Quantity",
                    "data": "quantity",
                    "class": "counterData",
                    "name": "kit_items.quantity",
                    "visible": false
                },
                {
                    "title": "On Hand",
                    "data": "quantity_on_hand",
                    "name": "quantity_on_hand",
                    "visible": false
                },
                {
                    "title": "Allocated",
                    "data": "quantity_allocated",
                    "name": "quantity_allocated",
                    "visible": false
                },
                {
                    "title": "Backorder",
                    "data": "quantity_backordered",
                    "name": "orders.quantity_backordered",
                    "visible": false
                },
                {
                    "title": "SKU",
                    "data": "sku",
                    "name": "products.sku",
                    "visible": false
                },
                {
                    "title": "Warehouse",
                    "data": "warehouse",
                    "name": "products.warehouse",
                    "visible": false
                },
                {
                    "title": "Barcode",
                    "data": "barcode",
                    "name": "barcode",
                    "visible": false
                },
                {
                    "title": "Quantity Pending",
                    "data": "quantity_pending",
                    "name": "products.quantity_pending",
                    "visible": false
                },
                {
                    "title": "Value",
                    "data": "value",
                    "name": "products.value",
                    "visible": false
                },
                {
                    "title": "Price",
                    "data": "price",
                    "name": "products.price",
                    "visible": false
                },
            ],
            createdRow: function (row, data, dataIndex) {
                $('td:eq(2)', row).css('min-width', '200px');
            }
        });
    }


    $(document).on('click', '.kit-title-icons .smallButtonsContainer', function () {
        $('.update-kit-quantity i').removeClass('d-none')
    });

    $(document).on('keydown', '.counterInput', function (event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            return false;
        }
    });

    $(document).on('click', '.edit-kit-content', function () {
        $('#product-kits-table-container').addClass('d-none')
        $('#edit-kit-items').removeClass('d-none')
        $('#rows_container').removeClass('d-none')
    });

    $('#chk-lot_tracking').on('change', function () {

        if ($(this).prop('checked')) {
            $('#lot_priority_container').removeClass('d-none');
            $('#lot_priority_container').addClass('d-flex');
        } else {
            $('#lot_priority_container').removeClass('d-flex');
            $('#lot_priority_container').addClass('d-none');
            $('#input-lot_priority').val(0);
        }
    });

    $(document).ready(function () {
        auditLog();
    })
};
