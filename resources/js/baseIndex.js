window.datatables = []
window.dtInstances = []
window.exportFilters = []

window.BaseIndex = function () {
    $(document).ready(function () {
        $.fn.dataTable.ext.errMode = 'none';

        window.datatables.forEach(function (config, index) {
            let datatableSelector = config.selector
            let containerSelector = config.selector + '-container'
            let filtersSelector = config.selector + '-filters'
            let columnsSelector = config.selector + '-columns'
            let filtersTags = filtersSelector + ' .select-ajax-tags'
            let globalSelector = $(datatableSelector).closest('.global-container')
            const datatableResource = config.resource

            if ($(datatableSelector).length < 1) {
                throw `Missing table element: ${datatableSelector}`
            }

            config.order = getDatatableOrder(datatableSelector, config)

            if (typeof config.drawCallback === 'function') {
                config.drawCallbackOverride = config.drawCallback;
                config.drawCallback = null;
            }

            let datatableMergedConfig = Object.assign(
                {},
                {
                    sDom: 'l<"row view-filter"<"col-sm-12"<"clearfix">>>t<"row view-pager"<"col-sm-12"<"text-center"ip>>>',
                    pagingType: 'simple',
                    language: window.datatableGlobalLanguage,
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    lengthMenu: [[10,25,50,100], [10,25,50,100]],
                    lengthChange: true,
                    info: false,
                    bInfo : false,
                    order: [],
                    columnDefs: [],
                    preDrawCallback: function(settings) {
                        if ($.fn.DataTable.isDataTable(datatableSelector)) {
                            let dt = $(datatableSelector).DataTable();

                            //Abort previous ajax request if it is still in process.
                            let settings = dt.settings();
                            if (settings[0].jqXHR) {
                                settings[0].jqXHR.abort();
                            }
                        }
                    },
                    initComplete: function (settings, json) {
                        let searchText = $(containerSelector + ' .searchText');

                        searchText.on('change keypress', $.fn.debounce((event) => {
                            if (event.type == 'keypress' && event.key != 'Enter') {
                                return;
                            }

                            let term = searchText.val();

                            if (term) {
                                window.dtInstances[datatableSelector]
                                    .search($(containerSelector + ' .searchText').val())
                                    .draw()
                            } else if (term === '') {
                                window.dtInstances[datatableSelector]
                                    .search('')
                                    .draw()
                            }

                            $('#select-all-checkboxes').prop('checked', false)
                        }, 300))

                        $(datatableSelector).on( 'processing.dt', function ( e, settings, processing ) {
                            if(processing) {
                                $('.table-responsive').addClass('processing');
                            } else {
                                $('.table-responsive').removeClass('processing');
                            }
                            $('#processingIndicator').css( 'display', processing ? 'block' : 'none' );
                        } )
                            .dataTable();

                        $(filtersSelector + ' .ajax-user-input').select2({
                            dropdownParent: $(filtersSelector),
                        })

                        $(filtersSelector + ' .apply').click(function () {
                            window.dtInstances[datatableSelector]
                                .ajax
                                .reload()
                            $(filtersSelector).modal('hide')
                        })

                        $(document).on('click', 'button[type=reset]', function() {
                            $(filtersTags).val(null).trigger('change')
                            $(filtersSelector + ' input').val('')
                            $(filtersSelector + ' select').val('0').trigger('change.select2')
                            window.dtInstances[datatableSelector]
                                .ajax
                                .reload()
                        })

                        updateFooter(datatableSelector)
                    },
                    drawCallback: function(settings) {
                        $(globalSelector).find('.loading-container').removeClass('d-flex').addClass('d-none')
                        $(globalSelector).find('.table-responsive').removeClass('d-none')

                        const json = settings.json
                        const api = this.api()
                        const countRecordsUrl = $(containerSelector).find('.total-records').data('count-records-url')
                        const widgetUrl = $(containerSelector).find('.widget-card').data('widgetUrl')

                        if (!json && settings.ajax) {
                            $(datatableSelector + ' .dataTables_empty').text($(datatableSelector).data('disable-autoload-text'))

                            if ($(datatableSelector).data('disable-autoload-allow-load-button')) {
                                $(datatableSelector + ' .dataTables_empty').append($('<br />'))
                                    .append($('<a />')
                                        .addClass('datatable-load-results-button')
                                        .attr('href', '#')
                                        .text($(datatableSelector).data('disable-autoload-button-label'))
                                        .click(function (event) {
                                            window.dtInstances[datatableSelector].draw();

                                            event.preventDefault();
                                        })
                                    )
                            }
                        }

                        if (json && !$(columnsSelector + ' .colvis').children().length) {
                            let orderColumn = json.visibleFields.order_column
                            let orderDirection = json.visibleFields.order_direction

                            let configOrder = typeof config.order[0] === 'object'
                                ? config.order[0]
                                : config.order

                            let selectedDirection = (orderDirection !== '')
                                ? orderDirection
                                : configOrder[1]

                            let dataTableAPI = $(datatableSelector).dataTable().api()
                            let columns = dataTableAPI.settings().init().columns

                            dataTableAPI.columns().every(function (index) {
                                if (columns[index].orderable !== false) {
                                    let selectedColumn = (orderColumn !== '')
                                        ? orderColumn === index
                                        : configOrder[0] === index

                                    $(columnsSelector + ' .columns-order [name="order_column"]').append(`
                                        <option
                                            ${selectedColumn ? 'selected' : ''}
                                            value="${index}"
                                        >
                                            ${columns[index].title}
                                        </option>
                                    `)

                                    $(columnsSelector + ' .columns-order [name="order_direction"]')
                                        .val(selectedDirection)
                                        .trigger('change');
                                }

                                if (index === 0 || columns[index].non_hiddable || columns[index].title === '') {
                                    return
                                }

                                let visibleField = Object.values(json.visibleFields.column_ids).includes(index)
                                dataTableAPI.column(index).visible(visibleField)

                                $(columnsSelector + ' .colvis').append(`
                                    <div class="col-6">
                                        <input
                                            id="${columns[index].title + columnsSelector}"
                                            ${visibleField ? 'checked' : ''}
                                            class="colvisItem"
                                            type="checkbox"
                                            data-name="${columns[index].title + columnsSelector}"
                                            data-index="${index}"
                                            data-resource="${config.resource}"
                                        >
                                        <label
                                            class="font-xs font-weight-600"
                                            title="${columns[index].title}"
                                            for="${columns[index].title + columnsSelector}"
                                        >
                                            <span>${columns[index].title}</span>
                                        </label>
                                    </div>
                                `)
                            })

                            $(document).on('change', `${columnsSelector} .colvisItem, ${columnsSelector} .columns-order select`, function () {
                                let orderColumn = $(columnsSelector).find('[name="order_column"] option:selected').val()
                                let orderDirection = $(columnsSelector).find('[name="order_direction"] option:selected').val()
                                let checkedItems = $(columnsSelector).find('.colvis .colvisItem:checked')
                                let visibleIds = []

                                if (checkedItems.length) {
                                    checkedItems.each(function (key, value) {
                                        visibleIds.push($(value).data('index'))
                                    })
                                }

                                $.ajax({
                                    type: 'GET',
                                    serverSide: true,
                                    url: '/edit_columns/update/',
                                    data: {
                                        'orderColumn': orderColumn,
                                        'orderDirection': orderDirection,
                                        'visibleIds': visibleIds,
                                        'object': config.resource,
                                    },
                                })

                                if ($(this).hasClass('colvisItem')) {
                                    window.dtInstances[datatableSelector]
                                        .column($(this).data('index'))
                                        .visible($(this).is(':checked'))
                                } else {
                                    window.dtInstances[datatableSelector]
                                        .order([orderColumn, orderDirection])
                                        .ajax
                                        .reload()
                                }
                            })
                        }

                        updateFooter(datatableSelector)

                        if (json) {
                            if (countRecordsUrl) {
                                $.ajax({
                                    type: 'POST',
                                    url: countRecordsUrl,
                                    data: api.ajax.params(),
                                    success: function (data) {
                                        $(containerSelector).find('.total-records').html(data.results)
                                    }
                                })
                            }

                            if (widgetUrl) {
                                $.ajax({
                                    type: 'GET',
                                    serverSide: true,
                                    url: widgetUrl,
                                    data: api.ajax.params(),
                                    success: function (data) {
                                        $(containerSelector).find('.widget-card').html(data)
                                    }
                                })
                            }
                        }

                        updatePaginationButtons(datatableSelector);

                        if (typeof config.drawCallbackOverride === 'function') {
                            config.drawCallbackOverride();
                        }
                    },
                },
                config
            )

            if ($(datatableSelector).data('disable-autoload')) {
                datatableMergedConfig.deferLoading = 0;
            }

            window.dtInstances[datatableSelector] = $(datatableSelector).DataTable(
                datatableMergedConfig
            )

            $('#submit-filter-button').click(function(e){
                e.preventDefault();
                window.dtInstances[datatableSelector].draw();
            });

            $('.export-form').submit(function (e) {
                e.preventDefault()

                $(this).closest('.modal').modal('hide');

                $.post({
                    url: $(this).attr('action'),
                    data: window.exportFilters[datatableResource],
                    success: function (response, textStatus, request) {
                        let element = document.createElement('a')
                        element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(response))
                        element.setAttribute('download', request.getResponseHeader('X-Export-Filename'))
                        element.style.display = 'none'
                        document.body.appendChild(element)
                        element.click()
                        document.body.removeChild(element)
                    }
                })
            })
        })
    })

    function getDatatableOrder(datatableSelector, config) {
        let datatableOrder = $(datatableSelector).data('datatable-order')

        if (datatableOrder && config.columns[datatableOrder[0]].orderable !== false) {
            return datatableOrder
        }

        if ('order' in config && config.columns[config.order[0]].orderable !== false) {
            return config.order
        }

        return []
    }

    $(document).on('change', '.custom-datatable-checkbox', function() {
        const datatableCheckbox = $('.custom-datatable-checkbox')
        let enableBulkEdit = false

        datatableCheckbox.each(function (i, element) {
            if (element.checked) {
                enableBulkEdit = true
                return false
            }

            enableBulkEdit = false
        })

        if (enableBulkEdit) {
            $('#bulk-edit-btn').attr('hidden', false)
        } else {
            $('#bulk-edit-btn').attr('hidden', true)
        }
    })

    $(document).on('change', '.custom-datatable-checkbox', function() {
        if ($(this).prop('checked') === false) {
            $('#select-all-checkboxes').prop('checked', false)
        }
    })

    $(document).on('change', '#select-all-checkboxes', function() {
        const selectAllCheckboxes = $('#select-all-checkboxes')
        const datatableCheckboxes = $('.custom-datatable-checkbox')

        if (selectAllCheckboxes.prop('checked')) {
            datatableCheckboxes.each(function (i, element) {
                element.checked = true
            })

            $('#bulk-edit-btn').attr('hidden', false)
        } else {
            datatableCheckboxes.each(function (i, element) {
                element.checked = false
            })

            $('#bulk-edit-btn').attr('hidden', true)
        }
    })

    $('#submit-bulk-edit').click(function (e) {
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

                $('#bulk-edit-tags').empty()

                toastr.success('Updated successfully!')

                window.dtInstances[window.datatables[0].selector].ajax.reload()
            },
            error: function (response) {
                appendValidationMessages(modal, response)
            }
        })
    })

    function updateFooter(datatableSelector) {
        $(datatableSelector).find('tfoot').html(
            $(datatableSelector).find('thead').clone().html()
        )
    }

    function updatePaginationButtons(datatableSelector) {
        let dt = $(datatableSelector).DataTable();
        const nextButton = $(datatableSelector).closest('.dataTables_wrapper').find('.paginate_button.next');

        if (dt.rows().data().length < dt.page.len() || dt.page.len() === -1) {
            nextButton.addClass('disabled');
        } else {
            nextButton.removeClass('disabled');
        }
    }
}
