window.WidgetOrdersShipped = function () {
    $(document).ready(function () {
        let container = $('.orders-shipped-count')

        if (container.length) {
            $.ajax({
                url: "/orders/orders_shipped_count",
                context: document.body
            }).done(function(data) {
                container.html(data)
            });

        }
    })
};
