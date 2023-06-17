window.PackingSingleOrder = function (orderId, packingNote = null) {
    let packButtonClass = localStorage.getItem('pack-button-class');

    let route = $('#packing_form').attr('action')
    let success_route = $('#packing_form').attr('data-success')
    let bulkShipBatch = $('#packing_form').attr('data-bulk-ship-batch') == 'true'

    let toPackTotal = 0;
    let toPackNumWarning = false;
    let activePackage = 1;
    let packageCount = 1;
    let nextPackageName = 1;

    let packingState = [];
    let itemQuantityState = [];

    let packageTitle = 'Package';

    let shippingBoxHeightLocked = false;
    let shippingBoxWidthLocked = false;
    let shippingBoxLengthLocked = false;

    let serialNumberInput = $('[name="serial_number"]');

    $(document).on('click', '.ship-button, .ship-and-print-button', function () {
        const title = $(this).text();

        if ($(this).hasClass('ship-button')) {
            $('#confirm-dropdown').text(title).removeClass('confirm-ship-and-print-button').addClass('confirm-ship-button');
            localStorage.setItem('pack-button-class', '.ship-button');
        } else {
            $('#confirm-dropdown').text(title).removeClass('confirm-ship-button').addClass('confirm-ship-and-print-button');
            localStorage.setItem('pack-button-class', '.ship-and-print-button');
        }
    });

    if (packButtonClass) {
        $(packButtonClass).click();
    }

    function drawDimensions(activePackage) {
        $('#weight').val(packingState[activePackage]['weight']);
        $('#length').val(packingState[activePackage]['_length']);
        $('#width').val(packingState[activePackage]['width']);
        $('#height').val(packingState[activePackage]['height']);

        if (shippingBoxLengthLocked) {
            $('#length').prop('readonly', true);
        } else {
            $('#length').prop('readonly', false);
        }

        if (shippingBoxWidthLocked) {
            $('#width').prop('readonly', true);
        } else {
            $('#width').prop('readonly', false);
        }

        if (shippingBoxHeightLocked) {
            $('#height').prop('readonly', true);
        } else {
            $('#height').prop('readonly', false);
        }
    }

    function packageButtonsResort() {
        let packNum = 1;
        $('#package_buttons_container .show_package').each(function () {
            $(this).html(packageTitle + ' ' + packNum);

            let xButton = $(this).next('.package_button_close');
            xButton.show();

            if (packNum == 1) {
                xButton.hide();
            }
            packNum++;
        });

        activePackage = 1;
        nextPackageName = packNum;
    }

    function firstAvailablePackage() {
        return $('#package_buttons_container .show_package:first').attr('rel');
    }

    function runFunctions() {
        $('.package_button_close').unbind('click');
        $('.package_button_close').click(function () {
            let button = $(this);

            app.confirm(null, 'Are you sure you want to delete this package?', () => {
                let packageNumber = button.attr('rel');
                activePackage = packageNumber;

                do {
                    $('#package' + packageNumber + ' .order_item_row .unpack-item-button:first').click();
                } while ($('#package' + packageNumber + ' .order_item_row .unpack-item-button').length > 0);

                $('#package' + packageNumber).remove();
                $('#package_button_container_' + packageNumber).remove();

                packingState[packageNumber] = [];

                $('#show_package_' + firstAvailablePackage()).trigger('click');
                packageButtonsResort();
            });
        });

        $('.show_package').unbind('click');
        $('.show_package').click(function () {

            let blockNumber = $(this).attr('rel');

            for (let i = packageCount; i > 0; i--) {

                $('#package' + i).hide();
                $('#show_package_' + i).removeClass('active');
            }

            $('#package' + blockNumber).show();
            $('#show_package_' + blockNumber).addClass('active');

            activePackage = blockNumber;

            $('#shipping_box').val(packingState[activePackage]['box']).change()
        });

        $('#shipping_box').change(function () {
            let selectedOption = $(this).children(':selected')

            if (packingState[activePackage]['box'] != $(this).val()) {
                packingState[activePackage]['box'] = $(this).val()

                packingState[activePackage]['_length'] = selectedOption.data('length')
                packingState[activePackage]['width'] = selectedOption.data('width')
                packingState[activePackage]['height'] = selectedOption.data('height')

                if (selectedOption.data('height-locked') == 1) {
                    shippingBoxHeightLocked = true
                } else {
                    shippingBoxHeightLocked = false
                }

                if (selectedOption.data('length-locked') == 1) {
                    shippingBoxLengthLocked = true
                } else {
                    shippingBoxLengthLocked = false
                }

                if (selectedOption.data('width-locked') == 1) {
                    shippingBoxWidthLocked = true
                } else {
                    shippingBoxWidthLocked = false
                }
            }

            drawDimensions(activePackage)
        });


        $('#weight').change(function () {
            packingState[activePackage]['weight'] = $(this).val();
        });
        $('#length').change(function () {
            packingState[activePackage]['_length'] = $(this).val();
        });
        $('#width').change(function () {
            packingState[activePackage]['width'] = $(this).val();
        });
        $('#height').change(function () {
            packingState[activePackage]['height'] = $(this).val();
        });

        $('#name').change(function () {
            packingState[activePackage]['name'] = $(this).val();
        });
        $('#address').change(function () {
            packingState[activePackage]['address'] = $(this).val();
        });
        $('#address2').change(function () {
            packingState[activePackage]['address2'] = $(this).val();
        });
        $('#company_name').change(function () {
            packingState[activePackage]['company_name'] = $(this).val();
        });
        $('#company_number').change(function () {
            packingState[activePackage]['company_number'] = $(this).val();
        });
        $('#city').change(function () {
            packingState[activePackage]['city'] = $(this).val();
        });
        $('#zip').change(function () {
            packingState[activePackage]['zip'] = $(this).val();
        });
        $('#country_name').change(function () {
            packingState[activePackage]['country_name'] = $(this).val();
        });
        $('#email').change(function () {
            packingState[activePackage]['email'] = $(this).val();
        });
        $('#phone').change(function () {
            packingState[activePackage]['phone'] = $(this).val();
        });

        $('#shipping_method_id').change(function () {
            packingState[activePackage]['shipping_method'] = $(this).val();
        });


        $('.unpack-item-button').unbind('click');
        $('.unpack-item-button').click(function (e) {
            e.preventDefault();

            let itemRow = $(this).closest('tr');
            let orderItemId = itemRow.attr('rel');
            let locationId = 0;
            let pickedLocationId = parseInt(itemRow.attr('picked-location-id'));
            let toteId = parseInt(itemRow.attr('picked-tote-id'));
            let parentOrderItemId = itemRow.attr('parent-id');
            let unpackedParentRow = $(`.unpacked-items-table tr[rel="${parentOrderItemId}"]`);
            let packedParentKey = itemRow.attr('packed-parent-key');
            let serialNumber = itemRow.attr('serial-number');

            let newPickedNum = 0;
            let trId = 'order_item_LOCATION-ID_' + orderItemId + '_' + pickedLocationId + '_' + toteId;


            if (parseInt(itemRow.attr('picked-location-id')) > 0) {
                locationId = pickedLocationId;

                let pickedNumMax = parseInt($('#order_item_pick_max_' + locationId + '_' + orderItemId + '_' + toteId).val());
                let pickedNum = parseInt($('#order_item_pick_' + locationId + '_' + orderItemId + '_' + toteId).html());

                if ((pickedNum + 1) <= pickedNumMax) {
                    newPickedNum = pickedNum + 1;
                    $('#order_item_pick_' + locationId + '_' + orderItemId + '_' + toteId).html(newPickedNum);
                }
            } else {
                locationId = itemRow.attr('location');
            }

            let quantityBeginning = parseInt($('#order_item_quantity_beginning_' + locationId + '_' + orderItemId + '_' + pickedLocationId + '_' + toteId).val());

            itemQuantityState[orderItemId][locationId + '_' + toteId]--;
            itemQuantityState[orderItemId][0]--;

            $('#packed-total-' + orderItemId).val(itemQuantityState[orderItemId][0]);

            let quantityRemaining = quantityBeginning - itemQuantityState[orderItemId][locationId + '_' + toteId];

            console.log(quantityBeginning, orderItemId, locationId, toteId);

            let optionNewText = $('#' + activePackage + '_order_item_location_span_' + locationId + '_' + orderItemId + packedParentKey + serialNumber + '_' + pickedLocationId + '_' + toteId).html() + ' - ' + quantityRemaining;
            if (quantityRemaining == 1) {
                $('#item_' + orderItemId + '_locations').append($('<option>', {
                    value: locationId,
                    text: optionNewText
                }));
            }

            $('#item_' + orderItemId + '_locations' + ' option[value=' + locationId + ']').text(optionNewText);

            let beforeQuantityInThisPackage = $('#' + activePackage + '_order_item_quantity_form_' + locationId + '_' + orderItemId + packedParentKey + serialNumber + '_' + pickedLocationId + '_' + toteId).val();
            let nowQuantityInThisPackage = beforeQuantityInThisPackage - 1;

            $('#' + activePackage + '_order_item_quantity_span_' + locationId + '_' + orderItemId + packedParentKey + serialNumber + '_' + pickedLocationId + '_' + toteId).html(nowQuantityInThisPackage);
            $('#' + activePackage + '_order_item_quantity_form_' + locationId + '_' + orderItemId + packedParentKey + serialNumber + '_' + pickedLocationId + '_' + toteId).val(nowQuantityInThisPackage);

            $('#order_item_quantity_span_LOCATION-ID_' + orderItemId + '_' + pickedLocationId + '_' + toteId).html(parseInt($('#order_item_quantity_span_LOCATION-ID_' + orderItemId + '_' + pickedLocationId + '_' + toteId).html()) + 1);
            $('#order_item_quantity_form_LOCATION-ID_' + orderItemId + '_' + pickedLocationId + '_' + toteId).val(parseInt($('#order_item_quantity_form_LOCATION-ID_' + orderItemId + '_' + pickedLocationId + '_' + toteId).html()) + 1);

            const index = itemLocationIndex(orderItemId, locationId, toteId, serialNumber, packedParentKey);

            if (index > -1) {
                let productWeight = parseFloat($('#order_item_weight_form_LOCATION-ID_' + orderItemId + '_' + pickedLocationId + '_' + toteId).val());
                let thisPackageWeight = parseFloat(packingState[activePackage]['weight']);

                if (productWeight > 0) {
                    packingState[activePackage]['weight'] = thisPackageWeight - parseFloat(productWeight);
                }

                packingState[activePackage]['items'].splice(index, 1);
            }

            if (nowQuantityInThisPackage == 0) {
                itemRow.remove();
            }
            console.log(quantityRemaining, trId);
            if (quantityRemaining > 0) {
                $('#' + trId).show();
                $('#' + trId).attr('barcode', $('#' + trId).attr('barcode').replace('//', ''));
            }

            $('#global_packed').html(parseInt($('#global_packed').html()) - 1);

            drawDimensions(activePackage);

            if (unpackedParentRow.length > 0) {
                calculateKitQuantities(parentOrderItemId);
            }

            e.stopPropagation();
        });

        drawDimensions(activePackage);
    }

    function validatePackingForms(dontCheckPackedNum = false) {
        let kitsNotFullyPacked = false;

        $('.unpacked-items-table tr[parent-id]').each((i, unpackedItemRow) => {
            let orderItemId = $(unpackedItemRow).attr('rel');
            let toPackTotal = parseInt($(`#to-pack-total-${orderItemId}`).val());
            let packedTotal = parseInt($(`#packed-total-${orderItemId}`).val());

            if (packedTotal < toPackTotal) {
                kitsNotFullyPacked = true;

                return false;
            }
        });

        if (kitsNotFullyPacked) {
            app.alert(null, 'You must fully pack the kits!');
            return false;
        }

        if (!dontCheckPackedNum && toPackTotal > parseInt($('#global_packed').html())) {
            toPackNumWarning = true;
            app.confirm('Packing', 'You have unpacked items. Do you want to continue?', function () {
                startShip(true);
            })
            return false;
        } else {
            toPackNumWarning = false;
        }

        let errorMessage = '';
        let result = true;
        if (parseInt($('#shipping_method_id').val()) == 0) {
            errorMessage += 'Shipping method required<br/>';
            result = false;
        } else {
            let packNum = 0;
            packingState.map(function (packing, key) {
                if (packing['items'] != undefined) {
                    packNum++;

                    if (packing.items === undefined || packing.items.length == 0) {
                        errorMessage += 'There are no items in Package ' + packNum + '<br/>';
                        result = false;
                    }
                    if (packing.box === undefined) {
                        errorMessage += 'Shipping box required in Package ' + packNum + '<br/>';
                        result = false;
                    }
                    if (packing.weight === undefined) {
                        errorMessage += 'Shipping box Weight required in Package ' + packNum + '<br/>';
                        result = false;
                    }
                    if (packing.height === undefined) {
                        errorMessage += 'Shipping box Height required in Package ' + packNum + '<br/>';
                        result = false;
                    }
                    if (packing._length === undefined) {
                        errorMessage += 'Shipping box Length required in Package ' + packNum + '<br/>';
                        result = false;
                    }
                    if (packing.width === undefined) {
                        errorMessage += 'Shipping box Width required in Package ' + packNum + '<br/>';
                        result = false;
                    }
                }
            });
        }

        if (errorMessage) {
            app.alert('', errorMessage);
        }

        return result;
    }

    function createPackageItemBlock(blockNumber) {

        packingState[blockNumber] = [];
        packingState[blockNumber]['items'] = [];

        let packageBlock = `
            <div id="package${ blockNumber }" class="package_item">
                <div>
                    <table
                        id="package_listing_${ blockNumber }"
                        class="table col-12 package-items-table packed-items-table"
                    >
                        <thead>
                            <tr>
                                <th class="col-7">Item</th>
                                <th class="col-3 ${ bulkShipBatch ? 'd-none' : '' }">Location</th>
                                <th class="col-1">Quantity</th>
                                <th class="col-1">Unpack</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        `

        $('#package_container').append(packageBlock);

        let packageButton = '<li class="nav-item package_button_container position-relative" id="package_button_container_' + blockNumber + '">' +
            '<button type="button" class="show_package btn nav-link mb-sm-3 mb-md-0 active" rel="' + blockNumber + '" id="show_package_' + blockNumber + '">' + packageTitle + ' ' + nextPackageName + '</button>' +
            '<span class="package_button_close ' + (blockNumber == 1 ? 'd-none' : '') + ' " rel="' + blockNumber + '">x</span>' +
            '</li>';
        $('#package_buttons_container').append(packageButton);

        for (let i = 1; i < blockNumber; i++) {
            $('#package' + i).hide();
            $('#show_package_' + i).removeClass('active');

            packingState[blockNumber]['box'] = packingState[i]['box'];
        }

        activePackage = blockNumber;

        packingState[blockNumber]['weight'] = 0;

        $('#shipping_box').trigger('change');

        nextPackageName++;
        runFunctions();
    }

    function itemLocationIndex(orderItemId, locationId, toteId, serialNumber, packedParentKey) {
        let itemLocationIndex = false;
        let arrIndex = 0;

        packingState.map(function (packageArr, packageIndex) {
            if (packageIndex == activePackage && packingState[packageIndex]['items']) {
                packingState[packageIndex]['items'].findIndex(object => {
                    if (object.orderItem == orderItemId && object.location == locationId && object.tote == toteId && object.serialNumber == serialNumber && object.packedParentKey == packedParentKey) {
                        itemLocationIndex = arrIndex;
                    }

                    arrIndex++
                });
            }
        });

        return itemLocationIndex;
    }

    function sizingAdjustments() {
        if ($(window).width() > 1200) {
            $('.navbar-top').hide();
        } else {
            $('.navbar-top').show();
        }
    }

    function startShip(dontCheckPackedNum = false) {
        const confirmButton = $('#confirm-dropdown');
        const title = confirmButton.text();

        confirmButton.text('Processing, please wait...');
        confirmButton.prop('disabled', true);

        let validate = validatePackingForms(dontCheckPackedNum);
        if (validate) {
            let packingStateRE = [...packingState];
            packingStateRE.map(function (packing, key) {
                    if (packing['items'] == undefined) {
                        packingStateRE.splice(key, 1);
                    }
                }
            );

            packingStateRE = packingStateRE.map(el => Object.assign({}, el));
            packingStateRE.splice(0, 1);
            let packingStateString = JSON.stringify(packingStateRE);

            $('#packing_state').val(packingStateString);

            if ($('#input-printer_id').val() == 'pdf') {
                $('#input-printer_id').val(null);
            }

            $.ajax({
                url: route,
                type: 'POST',
                dataType: 'json',
                data: $('#packing_form').serialize(),
                success: function (response) {
                    if (response.batchStatus) {
                        if (response.success === false) {
                            $.each(response.batchStatus, function (orderId, status) {
                                $(`.bulk-ship-orders tr[data-id="${orderId}"] .bulk-ship-order-status`)
                                    .text(status ? 'Shipped' : 'Failed')

                                if (! status.success) {
                                    toastr.error('Order ' + orderId + ' was not shipped!')
                                }
                            })
                        }
                    }

                    if ($('#input-printer_id').val()) {
                        window.location.href = success_route;
                    } else {
                        let labels = '';

                        for (let i = 0; i < response.labels.length; i++) {
                            labels += '<a href="' + response.labels[i].url + '" target="_blank">' + response.labels[i].name + '</a><br />';

                        }
                        app.alert('Labels', labels, function() {
                           window.location.href = success_route;
                        }, '');
                    }
                },
                error: function (errorResponse) {
                    if (typeof errorResponse.responseJSON !== 'undefined') {
                        if (typeof errorResponse.responseJSON.message !== 'undefined') {
                            app.alert('', errorResponse.responseJSON.message);
                        } else {
                            $.each(errorResponse.responseJSON.errors, function (key, value) {
                                app.alert('', value);
                            })
                        }
                    } else {
                        app.alert('', 'Can not process the shipping. Please try with different shipping method.');
                    }

                    confirmButton.text(title);
                    confirmButton.prop('disabled', false);
                }
            });
        } else {
            confirmButton.text(title);
            confirmButton.prop('disabled', false);
        }
    }

    function setCountryCode(country) {
        $.ajax({
            type: 'GET',
            serverSide: true,
            url: '/site/getCountryCode',
            data: {
                'country': country
            },
            success: function(response) {
                $('#cont_info_country_code').text(response.results.country_code)
            }
        })
    }

    function calculateKitQuantities(parentOrderItemId) {
        let unpackedParentRow = $(`.unpacked-items-table tr[rel="${parentOrderItemId}"]`);

        let toPackPerKit = $(`#to-pack-per-kit-${parentOrderItemId}`).val();
        let kitsToPack = $(`#to-pack-total-${parentOrderItemId}`).val();
        // Assume everything is packed, then we go down the numbers
        let kitsPacked = $(`#to-pack-total-${parentOrderItemId}`).val();
        let kitsRemaining = 0;

        $(`.unpacked-items-table tr[parent-id="${parentOrderItemId}"]`).each((i, childRow) => {
            let childOrderItemId = $(childRow).attr('rel');

            let childToPack = $(`#to-pack-total-${childOrderItemId}`).val();
            let childPacked = $(`#packed-total-${childOrderItemId}`).val();

            if (childPacked == 0) {
                kitsPacked = 0;

                return false;
            }

            kitsPacked = Math.min(kitsPacked, Math.floor(childPacked / (childToPack / kitsToPack)));
        });

        kitsRemaining = kitsToPack - kitsPacked;

        $(`#packed-total-${parentOrderItemId}`).val(kitsPacked);
        $(`#order_item_quantity_span_LOCATION-ID_${parentOrderItemId}_`).text(kitsRemaining + ' kit' + (kitsRemaining > 1 ? 's' : ''));

        if (kitsPacked == kitsToPack) {
            unpackedParentRow.hide();
        } else {
            unpackedParentRow.show();
        }

        $(`#package_listing_${activePackage} tr[rel="${parentOrderItemId}"]`).each((i, packedParentRow) => {
            let packedPerKit = 0;
            let packedParentKey = $(packedParentRow).attr('packed-parent-key');

            packingState[activePackage]['items'].forEach((item) => {
                if (item.parentId == parentOrderItemId && item.packedParentKey == packedParentKey) {
                    packedPerKit++;
                }
            });

            if (packedPerKit == 0) {
                $(packedParentRow).remove();
            } else {
                $(`#${activePackage}_order_item_quantity_span_LOCATION-ID_${packedParentKey}_0_0`).text(packedPerKit == toPackPerKit ? '1 kit' : packedPerKit + '/' + toPackPerKit);
            }
        })
    }

    $(document).ready(function () {
        if (packingNote && packingNote.trim().length) {
            app.alert('Packing note', packingNote);
        }

        $('#barcode').keypress(function(event) {
            if (event.keyCode == 13) {
                return false;
            }

            let barcode = $(this).val() + event.key;

            if (barcode != '') {
                if ($('[barcode=' + barcode + ']').length > 0) {
                    $('[barcode=' + barcode + ']:first .pack-item-button').trigger('click');
                    $(this).val('');

                    return false;
                }
            }
        });

        $('#barcode').change(function(event) {
            let barcode = $(this).val();

            if (barcode != '') {
                $('[barcode=' + barcode + ']:first .pack-item-button').trigger('click');
                $(this).val('');
            }
        });

        $('.shipping_contact_info_set').click(function () {
            $('#cont_info_name').html($('#input-shipping_contact_information\\[name\\]').val());
            $('#cont_info_company_name').html($('#input-shipping_contact_information\\[company_name\\]').val());
            $('#cont_info_company_number').html($('#input-shipping_contact_information\\[company_number\\]').val());
            $('#cont_info_address').html($('#input-shipping_contact_information\\[address\\]').val());
            $('#cont_info_address2').html($('#input-shipping_contact_information\\[address2\\]').val());
            $('#cont_info_zip').html($('#input-shipping_contact_information\\[zip\\]').val());
            $('#cont_info_city').html($('#input-shipping_contact_information\\[city\\]').val());
            $('#cont_info_email').html($('#input-shipping_contact_information\\[email\\]').val());
            $('#cont_info_phone').html($('#input-shipping_contact_information\\[phone\\]').val());
            $('#cont_info_country_name').text($('[name="shipping_contact_information[country_id]"]').select2('data')[0].text);
            $('#cont_info_country_code').text($('[name="shipping_contact_information[country_id]"]').select2('data')[0].country_code);
        });

        let packedItemsObj = [];

        $('#add_package').click(function () {
            packageCount++;
            createPackageItemBlock(packageCount);
        });

        function packItem(itemRow, serialNumber = '') {
            let hideRow = false;
            let orderItemId = itemRow.attr('rel');
            let locationId = 0;
            let locationName = '';
            let pickedLocationId = parseInt(itemRow.attr('picked-location-id'));
            let toteId = parseInt(itemRow.attr('picked-tote-id'));
            let toteName = itemRow.attr('picked-tote-name');
            let newPickedNum = 0;
            let parentOrderItemId = itemRow.attr('parent-id');
            let unpackedParentRow = $(`.unpacked-items-table tr[rel="${parentOrderItemId}"]`);

            let createParentRow = true;
            let packedParentKey = `_${Date.now()}`;
            let packedParentRowId = `${activePackage}_order_item_LOCATION-ID_${parentOrderItemId}${packedParentKey}`;

            if (parentOrderItemId) {
                $(`.packed-items-table tr[package=${activePackage}][rel=${parentOrderItemId}]`).each((i, packedParentRow) => {
                    let toPackPerKit = parseInt($(packedParentRow).attr(`to-pack-per-kit-${orderItemId}`));
                    let packedPerKit = parseInt($(packedParentRow).attr(`packed-per-kit-${orderItemId}`));

                    if (packedPerKit < toPackPerKit) {
                        createParentRow = false;
                        packedParentRowId = $(packedParentRow).attr('id');
                        packedParentKey = $(packedParentRow).attr('packed-parent-key');

                        return false;
                    }
                });
            } else {
                createParentRow = false;
                packedParentKey = '';
            }

            let packedParentRow = $(`#${packedParentRowId}`);
            let packedRow = $('#' + activePackage + '_order_item_' + locationId + '_' + orderItemId + packedParentKey);

            if (pickedLocationId > 0) {
                locationId = parseInt(itemRow.attr('picked-location-id'));
                locationName = itemRow.attr('picked-location-name');
                console.log("first", locationId, locationName);
            } else {
                locationId = $('#item_' + orderItemId + '_locations' + ' option').filter(':selected').val();
                locationName = $('#item_' + orderItemId + '_locations' + ' option:selected').text();
                locationName = locationName.substr(0, locationName.indexOf(' - '));
            }
            console.log(locationId, locationName);

            let orderItemLocationObj = {
                orderItem: orderItemId,
                location: locationId,
                tote: toteId,
                serialNumber: serialNumber,
                parentId: parentOrderItemId,
                packedParentKey: packedParentKey
            };

            let itemExistsInPackage = itemLocationIndex(orderItemId, locationId, toteId, serialNumber, packedParentKey);

            let orderItemKey = itemRow.attr('key');
            let productWeight = parseFloat($('#order_item_weight_form_LOCATION-ID_' + orderItemId + '_' + pickedLocationId + '_' + toteId).val());
            let thisPackageWeight = parseFloat(packingState[activePackage]['weight']);
            let quantityBeginning = parseInt($('#order_item_quantity_beginning_' + locationId + '_' + orderItemId + '_' + pickedLocationId + '_' + toteId).val());

            if (itemQuantityState[orderItemId] == undefined) {
                itemQuantityState[orderItemId] = [];
                itemQuantityState[orderItemId][locationId + '_' + toteId] = 0;
                itemQuantityState[orderItemId][0] = 0;
            } else if (itemQuantityState[orderItemId][locationId + '_' + toteId] == undefined) {
                itemQuantityState[orderItemId][locationId + '_' + toteId] = 0;
            }

            if (parseInt($('#to-pack-total-' + orderItemId).val()) > itemQuantityState[orderItemId][0]) {
                if (parseInt(itemRow.attr('picked-location-id')) > 0) {
                    let pickedNum = parseInt($('#order_item_pick_' + locationId + '_' + orderItemId + '_' + toteId).html());
                    newPickedNum = pickedNum - 1;
                    $('#order_item_pick_' + locationId + '_' + orderItemId + '_' + toteId).html(newPickedNum);
                    if (newPickedNum == 0) {
                        hideRow = true;
                    }
                } else {
                    let foundPickedLocation = false;
                    $('.picked_' + orderItemId).each(function() {
                        if (parseInt($(this).html()) > 0) {
                            foundPickedLocation = true;
                        }
                    });

                    if (foundPickedLocation) {
                        app.alert('Packing', 'Please pack the items from picked locations first.')
                        return;
                    }
                }

                let beforeQuantityInThisPackage = 0;
                itemQuantityState[orderItemId][0]++;
                itemQuantityState[orderItemId][locationId + '_' + toteId]++;
                $('#packed-total-' + orderItemId).val(itemQuantityState[orderItemId][0]);
                let quantityRemaining = quantityBeginning - itemQuantityState[orderItemId][locationId + '_' + toteId];
                let quantityRemainingGlobal = $('#to-pack-total-' + orderItemId).val() - $('#packed-total-' + orderItemId).val();

                if (itemExistsInPackage === false || createParentRow) {
                    if (unpackedParentRow.length > 0 && $(packedParentRow).length == 0) {
                        let parentRowHtml = unpackedParentRow[0].outerHTML;

                        parentRowHtml = parentRowHtml.replace('order_item_location_span_LOCATION-ID_' + parentOrderItemId, activePackage + '_order_item_location_span_LOCATION-ID_' + packedParentKey);
                        parentRowHtml = parentRowHtml.replace('order_item_picked_span_LOCATION-ID_' + parentOrderItemId, activePackage + '_order_item_picked_span_LOCATION-ID_' + packedParentKey);
                        parentRowHtml = parentRowHtml.replace('order_item_LOCATION-ID_' + parentOrderItemId, packedParentRowId);
                        parentRowHtml = parentRowHtml.replace('order_item_quantity_span_LOCATION-ID_' + parentOrderItemId, activePackage + '_order_item_quantity_span_LOCATION-ID_' + packedParentKey);
                        parentRowHtml = parentRowHtml.replace('order_item_quantity_form_LOCATION-ID_' + parentOrderItemId, activePackage + '_order_item_quantity_form_LOCATION-ID_' + packedParentKey);
                        parentRowHtml = parentRowHtml.replace('order_item_unpack_LOCATION-ID_' + parentOrderItemId, activePackage + '_order_item_unpack_LOCATION-ID_' + packedParentKey);
                        parentRowHtml = parentRowHtml.replace('order_item_id_form_LOCATION-ID_' + parentOrderItemId, activePackage + '_order_item_id_form_LOCATION-ID_' + packedParentKey);
                        parentRowHtml = parentRowHtml.replace('order_item_location_form_LOCATION-ID_' + parentOrderItemId, activePackage + '_order_item_location_form_LOCATION-ID_' + packedParentKey);
                        parentRowHtml = parentRowHtml.replace('order_item_tote_form_LOCATION-ID_' + parentOrderItemId, activePackage + '_order_item_tote_form_LOCATION-ID_' + packedParentKey);
                        parentRowHtml = parentRowHtml.replace('order_item_weight_form_LOCATION-ID_' + parentOrderItemId, activePackage + '_order_item_weight_form_LOCATION-ID_' + packedParentKey);
                        parentRowHtml = parentRowHtml.replace('order_item_serial_number_LOCATION-ID_' + parentOrderItemId, activePackage + '_order_item_serial_number_LOCATION-ID_' + packedParentKey);

                        packedParentRow = $(parentRowHtml);

                        $('#package_listing_' + activePackage + ' tbody').append(packedParentRow);

                        packedParentRow.attr('package', activePackage);
                        packedParentRow.attr('packed-parent-key', packedParentKey);
                    }

                    let rowHtml = itemRow[0].outerHTML;

                    rowHtml = rowHtml.replace(/LOCATION-ID/g, locationId);
                    rowHtml = rowHtml.replace(/TOTE-ID/g, toteId > 0 ? toteId : '');
                    rowHtml = rowHtml.replace(/SERIAL-NUMBER/g, serialNumber);

                    rowHtml = rowHtml.replace('order_item_location_span_' + locationId + '_' + orderItemId, activePackage + '_order_item_location_span_' + locationId + '_' + orderItemId + packedParentKey + serialNumber);
                    rowHtml = rowHtml.replace('order_item_picked_span_' + locationId + '_' + orderItemId, activePackage + '_order_item_picked_span_' + locationId + '_' + orderItemId + packedParentKey + serialNumber);
                    rowHtml = rowHtml.replace('order_item_' + locationId + '_' + orderItemId, activePackage + '_order_item_' + locationId + '_' + orderItemId + packedParentKey + serialNumber);
                    rowHtml = rowHtml.replace('order_item_quantity_span_' + locationId + '_' + orderItemId, activePackage + '_order_item_quantity_span_' + locationId + '_' + orderItemId + packedParentKey + serialNumber);
                    rowHtml = rowHtml.replace('order_item_quantity_form_' + locationId + '_' + orderItemId, activePackage + '_order_item_quantity_form_' + locationId + '_' + orderItemId + packedParentKey + serialNumber);
                    rowHtml = rowHtml.replace('order_item_unpack_' + locationId + '_' + orderItemId, activePackage + '_order_item_unpack_' + locationId + '_' + orderItemId + packedParentKey + serialNumber);
                    rowHtml = rowHtml.replace('order_item_id_form_' + locationId + '_' + orderItemId, activePackage + '_order_item_id_form_' + locationId + '_' + orderItemId + packedParentKey + serialNumber);
                    rowHtml = rowHtml.replace('order_item_location_form_' + locationId + '_' + orderItemId, activePackage + '_order_item_location_form_' + locationId + '_' + orderItemId + packedParentKey + serialNumber);
                    rowHtml = rowHtml.replace('order_item_tote_form_' + locationId + '_' + orderItemId, activePackage + '_order_item_tote_form_' + locationId + '_' + orderItemId + packedParentKey + serialNumber);
                    rowHtml = rowHtml.replace('order_item_weight_form_' + locationId + '_' + orderItemId, activePackage + '_order_item_weight_form_' + locationId + '_' + orderItemId + packedParentKey + serialNumber);
                    rowHtml = rowHtml.replace('order_item_serial_number_' + locationId + '_' + orderItemId, activePackage + '_order_item_serial_number_' + locationId + '_' + orderItemId + packedParentKey + serialNumber);

                    packedRow = $(rowHtml);

                    if (serialNumber) {
                        packedRow.find('.order_item_serial_number').text(`S/N: ${serialNumber}`);
                    }

                    if (packedParentKey) {
                        $(`#package_listing_${activePackage} tbody tr[packed-parent-key="${packedParentKey}"]:last`).after(packedRow);
                    } else {
                        $('#package_listing_' + activePackage + ' tbody').append(packedRow);
                    }

                    packedRow.attr('package', activePackage);
                    packedRow.attr('packed-parent-key', packedParentKey);
                    packedRow.attr('serial-number', serialNumber);
                } else {
                    beforeQuantityInThisPackage = parseInt($('#' + activePackage + '_order_item_quantity_form_' + locationId + '_' + orderItemId + packedParentKey + serialNumber + '_' + pickedLocationId + '_' + toteId).val());
                }

                if (packedParentRow.length) {
                    packedParentRow.attr(`packed-per-kit-${orderItemId}`, parseInt(packedParentRow.attr(`packed-per-kit-${orderItemId}`)) + 1);
                }

                let nowQuantityInThisPackage = beforeQuantityInThisPackage + 1;

                $('#' + activePackage + '_order_item_location_span_' + locationId + '_' + orderItemId + packedParentKey + serialNumber + '_' + pickedLocationId + '_' + toteId).html((toteName != '' ? toteName + ' - ' : '') + locationName);
                $('#' + activePackage + '_order_item_picked_span_' + locationId + '_' + orderItemId + packedParentKey + serialNumber).hide();

                $('#' + activePackage + '_order_item_quantity_span_' + locationId + '_' + orderItemId + packedParentKey + serialNumber + '_' + pickedLocationId + '_' + toteId).html(nowQuantityInThisPackage);
                $('#' + activePackage + '_order_item_quantity_form_' + locationId + '_' + orderItemId + packedParentKey + serialNumber + '_' + pickedLocationId + '_' + toteId).val(nowQuantityInThisPackage);

                $('#' + activePackage + '_order_item_unpack_' + locationId + '_' + orderItemId + packedParentKey + serialNumber + '_' + pickedLocationId + '_' + toteId).removeClass('d-none');

                $('#order_item_quantity_span_LOCATION-ID_' + orderItemId + '_' + pickedLocationId + '_' + toteId).html(toteId == 0 ? quantityRemainingGlobal : newPickedNum);
                $('#order_item_quantity_form_LOCATION-ID_' + orderItemId + '_' + pickedLocationId + '_' + toteId).val(toteId == 0 ? quantityRemainingGlobal : newPickedNum);

                if (quantityRemaining == 0) {
                    $('#item_' + orderItemId + '_locations_' + pickedLocationId + '_' + toteId + ' option[value=' + locationId + ']').remove();
                }

                if (quantityRemainingGlobal == 0) {
                    hideRow = true;
                }

                let keyName = orderItemKey + '_' + orderItemId + packedParentKey + '_' + serialNumber + '_' + locationId + '_' + toteId + '_' + activePackage;

                $('#' + activePackage + '_order_item_quantity_form_' + locationId + '_' + orderItemId + packedParentKey + serialNumber + '_' + pickedLocationId + '_' + toteId).attr('name', 'order_items[' + keyName + '][quantity]');
                $('#' + activePackage + '_order_item_id_form_' + locationId + '_' + orderItemId + packedParentKey + serialNumber + '_' + pickedLocationId + '_' + toteId).attr('name', 'order_items[' + keyName + '][order_item_id]');
                $('#' + activePackage + '_order_item_location_form_' + locationId + '_' + orderItemId + packedParentKey + serialNumber + '_' + pickedLocationId + '_' + toteId).attr('name', 'order_items[' + keyName + '][location_id]');
                $('#' + activePackage + '_order_item_tote_form_' + locationId + '_' + orderItemId + packedParentKey + serialNumber + '_' + pickedLocationId + '_' + toteId).attr('name', 'order_items[' + keyName + '][tote_id]');

                packingState[activePackage]['items'].push(orderItemLocationObj);

                if (productWeight > 0) {
                    packingState[activePackage]['weight'] = thisPackageWeight + parseFloat(productWeight * 1);
                }

                $('#global_packed').html(parseInt($('#global_packed').html()) + 1);

                let optionNewText = locationName + ' - ' + quantityRemaining;
                $('#item_' + orderItemId + '_locations' + ' option[value=' + locationId + ']').text(optionNewText);
            } else {
                app.alert(null, 'You packed all items of this product');
            }

            if (hideRow) {
                itemRow.hide();
                itemRow.attr('barcode', '//' + itemRow.attr('barcode'));
            }

            runFunctions();

            if (unpackedParentRow.length > 0) {
                calculateKitQuantities(parentOrderItemId);
            }
        }

        $('.pack-item-button').click(function (event) {
            let itemRow = $(this).closest('tr');

            if (itemRow.attr('has-serial-number') == 1) {
                serialNumberInput.val('');

                const modal = $('#pack-item-serial-number-input-modal');

                modal.modal('show');
                modal.data('item-row', itemRow)
            } else {
                packItem(itemRow);
            }
        });

        $('#pack-item-serial-number-input-modal').on('shown.bs.modal', () => {
            serialNumberInput.focus();
        });

        $('#serial-number-set-button').click(function (event) {
            let itemRow = $('#pack-item-serial-number-input-modal').data('item-row');
            let serialNumber = serialNumberInput.val().trim();

            if (!serialNumber) {
                toastr.error('No serial number input, product is not packed');
            } else if ($(`[serial-number="${serialNumber}"]`).length) {
                toastr.error('Serial number already used!');
            } else {
                packItem(itemRow, serialNumber);
            }

            $('#barcode').focus();
        });

        serialNumberInput.keydown(function (event) {
            if (event.keyCode === 13) {
                $('#serial-number-set-button').click();
                event.preventDefault();
            }
        });

        $('.confirm-ship-button, .confirm-ship-and-print-button').click(function () {
            let printPackingSlip = $('[name="print_packing_slip"]');

            if ($(this).hasClass('confirm-ship-and-print-button')) {
                printPackingSlip.val(true);
            } else {
                printPackingSlip.val(null);
            }

            if ($('[name="shipping_method_id"]').val() == 'dummy') {
                app.confirm(null, 'Are you sure you want to ship using dummy label?', startShip, null, null, '');
            } else {
                startShip();
            }
        });

        createPackageItemBlock(packageCount);

        $('.sidenav-toggler').removeClass('active');
        $('.sidenav-toggler').data('action', 'sidenav-pin');
        $('body').removeClass('g-sidenav-pinned').removeClass('g-sidenav-show').addClass('g-sidenav-hidden');
        $('body').find('.backdrop').remove();

        sizingAdjustments()

        $(window).resize(function () {
            sizingAdjustments();
        })

        $('.to-pack-total:not(.to-pack-total-skip-calculation)').each(function () {
            toPackTotal += parseInt($(this).val());
        })

        $('#global_to_packed').html(toPackTotal)

        $('#shipping_box').trigger('change')

        $('#select-drop-point-modal').on('shown.bs.modal', function () {
            let dropPointSelect = $('.drop_point_id')
            const dropPointData = $('#select-drop-points-button')

            let dropPointLocatorData = dropPointSelect.data('ajax--url')
                + '?zip=' + $('#cont_info_zip').text()
                + '&city=' + $('#cont_info_city').text()
                + '&address=' + $('#cont_info_address').text()
                + '&country_code=' + $('#cont_info_country_code').text()
                + '&order_id=' + orderId
                + '&shipping_method_id=' + $('[name="shipping_method_id"]').val()

            dropPointSelect.select2('destroy')
            dropPointSelect.data('ajax--url', dropPointLocatorData)
            dropPointSelect.select2({
                dropdownParent: $(this)
            })
        })

        $('.drop_point_id').on('select2:select', function () {
            $('#drop_point_id').val($(this).val())
            $('#drop-point-info').attr('hidden', false)
            $('#drop-point-details').text($(this).text())
        })

        if ($('#input-shipping_method_id').val() !== undefined) {
            checkDropPointsForShippingMethod()
        }

        $('#input-shipping_method_id').on('change', function () {
            checkDropPointsForShippingMethod()
        })

        function checkDropPointsForShippingMethod() {
            let method = $('#input-shipping_method_id').val()
            const requireDropPoint = $('#check-drop-point-' + method + '').val()

            if (requireDropPoint === '1') {
                $('#drop-point-modal').attr('hidden', false)

                const dropPointData = $('#select-drop-points-button')

                if (dropPointData.data('shipping-method-name') !== 'null') {
                    let dropPointSelect = $('.drop_point_id')

                    let dropPointAjax = dropPointSelect.data('ajax--url')
                        + '?zip=' + $('#cont_info_zip').text()
                        + '&city=' + $('#cont_info_city').text()
                        + '&address=' + $('#cont_info_address').text()
                        + '&country_code=' + $('#cont_info_country_code').text()
                        + '&order_id=' + orderId
                        + '&preselect=' + true
                        + '&shipping_method_id=' + $('[name="shipping_method_id"]').val()

                    $.ajax({
                        type: 'GET',
                        serverSide: true,
                        url: dropPointAjax,
                        success: function(response) {
                            if (response.results.length > 0) {
                                const initialDropPoint = response.results[0]

                                $('#drop_point_id').val(initialDropPoint.id)
                                $('#drop-point-info').attr('hidden', false)
                                $('#drop-point-details').text(initialDropPoint.text)
                            }
                        }
                    })
                }

            } else {
                $('#drop-point-modal').attr('hidden', true)
                $('#drop-point-info').attr('hidden', true)
            }
        }
    });
};
