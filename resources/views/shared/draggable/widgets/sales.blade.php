<div class="card border-8 py-3 px-4 sales-widget" data-shortcode="[widget_sales_info]">
    <div class="border-bottom-gray py-3">
            <h2 class="font-weight-600 text-black d-flex align-items-center">
                <i class="picon-tag-light mr-3"></i>
                {{ __('Sales') }}
            </h2>
        </div>
    <div class="widgetContent d-none" id="sales"></div>
    <div class="widgetLoadingContainer d-flex justify-content-center align-items-center p-5">
        <img width="50px" src="{{ asset('img/loading.gif') }}" alt="">
    </div>
</div>
