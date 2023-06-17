<div class="card border-8 h-100 d-flex">
        <div class="card-header px-4 border-8">
            <div class="d-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M21 8V21H3V8" stroke="#5B5B5B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M23 3H1V8H23V3Z" stroke="#5B5B5B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M10 12H14" stroke="#5B5B5B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <h3 class="mb-0 d-inline mx-2">{{ __('Revenue & Orders') }}</h3>
                <span class="badge badge-dot ml-2">
                  <i style="background-color: #ECE9F1;"></i>
                  <span class="status text-capitalize">{{ __('Revenue') }}</span>
                </span>
                <span class="badge badge-dot ml-2">
                  <i style="background-color: #f39200;"></i>
                  <span class="status text-capitalize">{{ __('Orders') }}</span>
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="chart">
                <canvas id="revenue-profit-chart" class="chart-canvas"></canvas>
            </div>
        </div>
    </div>

