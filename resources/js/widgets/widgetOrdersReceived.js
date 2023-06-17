window.WidgetOrdersReceived = function () {
    let container = $('.orders-received-count')

    if (container.length) {
        $(document).ready(function () {
            $.ajax({
                url: "/orders/orders_received_count",
                context: document.body
            }).done(function(data) {
                container.html(data)
            });
        })
    }
};
