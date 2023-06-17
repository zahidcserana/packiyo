<button class="delete-widget btn btn-sm btn-danger">X</button>
<div class="card m-0 h-100 d-flex" data-shortcode="[widget_revenue_over_time]">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="d-flex align-items-center mx-3">
                <i class="picon-trending-up-light mr-2"></i>
                <h3 class="mb-0 d-inline mx-2">{{ __('Revenue & Orders') }}</h3>
            </div>
            <div class="col text-right">
                <span class="badge badge-dot ml-2">
                  <i class="gray-badge-dot" style=""></i>
                  <span class="status text-capitalize">{{ __('Revenue') }}</span>
                </span>
                <span class="badge badge-dot ml-2">
                  <i class="orange-badge-dot" style=""></i>
                  <span class="status text-capitalize">{{ __('Orders') }}</span>
                </span>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="chart">
            <canvas id="revenue-profit-chart" class="chart-canvas" ></canvas>
        </div>
    </div>
</div>
