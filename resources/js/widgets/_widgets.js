window.Widget = function (showGeoWidget) {
    $(document).ready(function () {
        $(document).on('click', '#filter-widgets', function () {
            WidgetSales();
            WidgetTopSellingItems();
            RevenueChart();
            DashboardMap();
        })

        $('.card-calendar').addClass('with-loader with-opacity-background');

        $('body').on('gridstack-loaded', function () {
            setTimeout(function () {
                RevenueChart();
                WidgetSales();
                WidgetTopSellingItems();
                WidgetInfoTabs();
                RevenueChart();

                if ( ! $.fn.DataTable.isDataTable( '#purchase-orders-received' ) ) {
                    PurchaseOrdersReceived();
                }

                if ($('#map').length) {
                    DashboardMap();
                }

                /*
                if ( ! $.fn.DataTable.isDataTable( '#order-table' ) ) {
                    //Order();
                }
                */

                // if ( ! $.fn.DataTable.isDataTable( '#late-orders' ) ) {
                //     LateOrders();
                // }

                $.each($('.grid-stack-item'), function(key,value){
                    let attrValue = $(value).find('.card').data('shortcode');
                    let className = attrValue.substring(attrValue.indexOf('[') + 1, attrValue.indexOf(']'));
                    $(this).addClass(className);
                })

                $('.card-calendar').removeClass('with-loader with-opacity-background');
            }, 1000)
        });

        $.ajax({
            type: "GET",
            data: {location: 'dashboard'},
            url:  'user_widgets/get',
            success: function(data) {
                let grid = GridStack.init({
                    minRow: 1,
                    column: 12,
                    staticGrid: true,
                    cellHeight: 1,
                    float: false,
                });

                //IF DATABASE IS EMPTY
                let serializedData = [
                    // {x: 0, y: 0, w: 2, h: 200, id: '1', noResize: true, content: '[widget_orders_table_dashboard]'},
                    // {x: 0, y: 0, w: 2, h: 200, id: '2', noResize: true, content: '[widget_orders_table]'},
                ];

                if (data.length) {
                    serializedData = data;
                }

                if (!showGeoWidget){
                    //temporary removing the 'widget_order_by_country'
                    serializedData.splice(5, 1);
                }

                let addListenerToDeleteButton = function () {
                    $('.delete-widget').on('click', function (){
                        grid.removeWidget(this.parentNode.parentNode)
                    })
                };

                let showHideDeleteButton = function () {
                    !grid.opts.staticGrid ? $('.delete-widget').show() : $('.delete-widget').hide();
                };

                let loadGrid = function() {
                    grid.load(serializedData, true);

                    addListenerToDeleteButton();
                    showHideDeleteButton();

                    $('body').trigger('gridstack-loaded');
                };

                let saveGrid = function() {
                    if (grid.engine.column === 12) {
                        serializedData = grid.save();
                        $.each(serializedData, function (key, value){
                            let attrValue = value.content.match(/data-shortcode="(.*?)"/)
                            if (attrValue) {
                                value.content = attrValue[1]
                            }
                        });

                        $.ajax({
                            method:'post',
                            url: 'user_widgets/save',
                            data: {
                                location: 'dashboard',
                                grid_stack_data: JSON.stringify(serializedData)
                            }
                        });
                    }
                };

                let clearGrid = function() {
                    grid.removeAll();
                };

                let staticToggle = function () {
                    grid.setStatic(!grid.opts.staticGrid)
                    showHideDeleteButton();
                };

                let calculateItemHeight = function (items) {
                    let itemsChanged = 0;

                    $(window).on('resize', function () {
                        if (grid.engine.column === 12) {
                            $('.grid-button').prop('disabled', false)
                        } else {
                            $('.grid-button').prop('disabled', true)
                        }
                    })

                    // Run only when in fullscreen
                    if (grid.engine.column === 12) {
                        if (items) {
                            let newSerializedData = serializedData

                            $.each(newSerializedData, function (key, value){
                                let gridItems = items;
                                let itemInset = $(gridItems[key].el).find('.grid-stack-item-content').css('inset').replace('px', '');
                                let itemHeightFromGrid = $(gridItems[key].el).find('.grid-stack-item-content')[0].scrollHeight;
                                let itemTotalHeightFromGrid = itemHeightFromGrid + (itemInset * 2);
                                if (value.h !==  itemTotalHeightFromGrid ) {
                                    itemsChanged++
                                    newSerializedData[key].h = itemTotalHeightFromGrid
                                }
                            })

                            if (itemsChanged) {
                                itemsChanged = 0

                                clearGrid();
                                grid.load(newSerializedData, true);
                                saveGrid();

                                $.ajax({
                                    type: "GET",
                                    data: {location: 'dashboard'},
                                    url:  'user_widgets/get',
                                    success: function(data)
                                    {
                                        serializedData = data;
                                        clearGrid();
                                        loadGrid();
                                    }
                                })
                            }
                        }
                    }
                }

                $('.save-grid').on('click', function () {
                    saveGrid();
                })

                $('.new-widget').on('click', function (){
                    let node = {
                        x: 0,
                        y: 0,
                        w: 50,
                        h: 50,
                        // noResize: true,
                        content:'[' + $('.widget-select').val() + ']'
                    };

                    grid.addWidget(node);
                    saveGrid();

                    $('.new-widget').prop('disabled', true)

                    $.ajax({
                        type: "GET",
                        data: {location: 'dashboard'},
                        url:  'user_widgets/get',
                        success: function(data)
                        {
                            serializedData = data;

                            clearGrid();
                            loadGrid();
                            // calculateItemHeight(itemsFromEvent);

                            $('.new-widget').prop('disabled', false)

                        }
                    })

                    return false;
                })

                $('.static').on('click', function () {
                    staticToggle()
                })

                // $('.recalculate').on('click', function () {
                //     calculateItemHeight(itemsFromEvent)
                // })

                let itemsFromEvent = [];

                grid.on('added change', function(e, items) {
                    itemsFromEvent = items;
                });

                loadGrid();
                // calculateItemHeight(itemsFromEvent);
            },
            //ERROR PLACE
            error: function (jqXHR, textStatus, errorThrown) { console.log(jqXHR, textStatus, errorThrown) }

        });
    });
}
