window.WidgetPurchaseOrdersComingIn = function () {
    $(document).ready(function () {
        let container = $('.purchase-orders-coming-in')

        if (container.length) {
            $.ajax({
                url: "/purchase_orders/coming_in",
                context: document.body
            }).done(function(data) {
                container.html(data.poCount)
            });
        }
    })
};
