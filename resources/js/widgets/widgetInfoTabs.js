window.WidgetInfoTabs = function (event) {
    $(document).ready(function(e) {
        let container = $(document).find('.info-widget')

        if (container.length) {
            container.find('.widgetLoadingContainer').addClass('d-flex').removeClass('d-none')
            container.find('.widgetContent').addClass('d-none')

            $.ajax({
                url: "/user_widgets/get_info",
                context: document.body
            }).done(function (data) {
                $(document).find('#info_tabs').html(data)

                container.find('.widgetLoadingContainer').removeClass('d-flex').addClass('d-none')
                container.find('.widgetContent').removeClass('d-none')
            })
        }
    });
};
