window.PurchaseOrderStatus = function () {
    $(document).ready(function() {
        $(document).on('click', '#purchase-order-status-table tbody tr', function (event) {
            if( document.getSelection().toString() === '' ) {
                window.location.href = $(event.target).parent().find('.edit').attr('href')
            }
        });
    });
};
