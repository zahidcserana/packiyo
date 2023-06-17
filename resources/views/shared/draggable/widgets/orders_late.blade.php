<button class="delete-widget btn btn-sm btn-danger">X</button>
<div class="card m-0 h-100 d-flex" data-shortcode="[widget_orders_late]">
    <div class="card-header">
        <div class="row align-items-center sts sts-pending card-sts">
            <div class="col col-auto pr-0">
                <i class="automagical-hourglass"></i>
            </div>
            <div class="col col-auto">
                <h3 class="mb-0">{{ __('Late Orders') }}</h3>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table items-table align-items-center col-12 p-0" id="late-orders" style="width: 100% !important;">
            <thead></thead>
            <tbody></tbody>
        </table>
    </div>
    <div class="card-body" style="padding: 14px; margin-top: 1px;">
        <div class="col text-center">
            <a href="{{route('order.index', ['from_orders_late' => true])}}">
                <span id="total_late_orders"></span> orders are late - View all
            </a>
        </div>
    </div>
</div>
