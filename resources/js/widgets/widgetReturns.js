window.WidgetReturns = function () {
    $(document).ready(function () {
        let container = $('.returns-count')

        if (container.length) {
            $.ajax({
                url: "/returns/returns-count",
                context: document.body
            }).done(function(data) {
                container.html(data)
            });

        }
    })
};
