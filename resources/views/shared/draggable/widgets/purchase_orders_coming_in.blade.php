<button class="delete-widget btn btn-sm btn-danger">X</button>
<div class="card transparent transparent-with-border m-0 h-100" data-shortcode="[widget_purchase_orders_coming_in]">
    <a href="{{route('purchase_orders.index')}}" class="btn-custom sts sts-on-hold h-100 d-flex justify-content-md-center">
        <div class="row m-0 align-items-center">
            <div class="col col-auto pl-0 pr-2 pr-lg-4 d-flex">
                <i class="automagical-products"></i>
            </div>
            <div class="col p-0 text-left">
                <strong class="purchase-orders-coming-in text-default"></strong>
                <p class="m-0 text-default">
                    {{ __('Purchase orders coming in') }}
                </p>
            </div>
        </div>
    </a>
</div>
