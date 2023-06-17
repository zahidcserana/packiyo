window.WidgetPurchaseOrdersReceivedQuantity = function () {
    $(document).ready(function () {
        let container = $('.purchase-orders-quantity-received')

        if (container.length) {
            $.ajax({
                url: "/purchase_orders/quantity_calc",
                context: document.body
            }).done(function(data) {
                container.html(data.poQuantityReceived)
            });

        }
    })
};
