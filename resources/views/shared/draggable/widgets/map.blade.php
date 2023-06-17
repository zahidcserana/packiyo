<div class="card h-100 d-flex" data-shortcode="[widget_order_by_country]" >
    <div class="card-header">
        <div class="row">
            <div class="col-12 d-flex align-items-center">
                <h3 class="mb-0 d-inline mr-2">{{ __('Orders by') }}</h3>
                <a class="btn btn-sm countries bg-orange text-white">{{ __('Country') }}</a>
                <a class="btn btn-sm btn-secondary cities">{{ __('City') }}</a>
            </div>
        </div>
    </div>
    <div class="card-body position-relative p-0 h-100">
        <div class="country-list"></div>
        <div class="city-list" style="display: none"></div>
        <div id="map" style="height: 830px; width: 100%;"></div>
    </div>
</div>
