window.WidgetSales = function (event) {
    $(document).ready(function(e) {
        let container = $(document).find('.sales-widget')

        if (container.length) {
            container.find('.widgetLoadingContainer').addClass('d-flex').removeClass('d-none')
            container.find('.widgetContent').addClass('d-none')

            $.ajax({
                url: "/user_widgets/get_sales",
                context: document.body,
                data: {
                    startDate: $(document).find('input[name="dashboard_filter_date_start"]').val(),
                    endDate: $(document).find('input[name="dashboard_filter_date_end"]').val()
                }
            }).done(function (data) {
                $(document).find('#sales').html(data)

                container.find('.widgetLoadingContainer').removeClass('d-flex').addClass('d-none')
                container.find('.widgetContent').removeClass('d-none')
            })
        }
    });
};
