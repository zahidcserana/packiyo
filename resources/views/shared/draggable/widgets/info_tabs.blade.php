<div class="mb-5 border-8 dashboard-info-tabs card bg-lightGrey info-widget"  data-shortcode="[widget_info_tabs]">
    <div class="widgetContent d-none">
        <div class="nav-wrapper">
            <ul class="nav nav-pills nav-fill flex-md-row justify-content-between" id="" role="tablist">
                <li class="nav-item">
                    <a class="nav-link mb-sm-3 mb-md-0 active" id="orders-tab" data-toggle="tab" href="#orders-tab-content" role="tab" aria-controls="tabs-icons-text-1" aria-selected="true">
                        <i class="picon-archive-light"></i>
                        <span class="nav-link-text">{{ __('Orders') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link mb-sm-3 mb-md-0" id="shipments-tab" data-toggle="tab" href="#shipments-tab-content" role="tab" aria-controls="tabs-icons-text-3" aria-selected="false">
                        <i class="picon-truck-light "></i>
                        <span class="nav-link-text">{{ __('Shipments') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link mb-sm-3 mb-md-0" id="products-tab" data-toggle="tab" href="#products-tab-content" role="tab" aria-controls="tabs-icons-text-3" aria-selected="false">
                        <i class="picon-box-light"></i>
                        <span class="nav-link-text">{{ __('Products') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link mb-sm-3 mb-md-0" id="purchases-tab" data-toggle="tab" href="#purchases-tab-content" role="tab" aria-controls="tabs-icons-text-3" aria-selected="false">
                        <i class="picon-shopping-bag-light"></i>
                        <span class="nav-link-text">{{ __('Purchases') }}</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="tab-content text-black bg-white border-12 p-3 inputs-container" id="info_tabs"></div>
    </div>
    <div class="card widgetLoadingContainer d-flex justify-content-center align-items-center p-5">
        <img width="50px" src="{{ asset('img/loading.gif') }}" alt="">
    </div>
</div>
