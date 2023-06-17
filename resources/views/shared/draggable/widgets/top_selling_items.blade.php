<div class="card border-8 py-3 px-4 top-selling-items" data-shortcode="[widget_top_selling_items]">
    <div class="border-bottom-gray py-3">
        <h2 class="font-weight-600 text-black d-flex align-items-center">
            <i class="picon-heart-light mr-3"></i>
            {{ __('Top Selling Items') }}
        </h2>
    </div>
    <div class="widgetContent d-none" id="top_selling_items"></div>
    <div class="widgetLoadingContainer d-flex justify-content-center align-items-center p-5">
        <img width="50px" src="{{ asset('img/loading.gif') }}" alt="">
    </div>
</div>
