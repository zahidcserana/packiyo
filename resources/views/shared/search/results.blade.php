<div class="d-none row global_search_results p-0 m-0 bg-white" id="global_search_results">
    <div class="col-12">
        <div class="row">
            <div class="col-6 bg-white">
                <div id="product_search_block" class="pt-2 search_result_box">
                    <div class="row">
                        <div class="col-6">
                            <h4>{{__('Products')}}</h4>
                        </div>
                        <div class="col-6 text-right">
                            <a href="#" id="product_see_all" class="text-logoOrange">{{__('See all')}}</a>
                        </div>
                    </div>
                    <div class="row">
                        <div id="product_search_container" class="col-12 p-4"></div>
                    </div>
                </div>

                <div id="order_search_block" class="pt-2 d-none search_result_box">
                    <div class="row">
                        <div class="col-6">
                            <h4>{{__('Orders')}}</h4>
                        </div>
                        <div class="col-6 text-right">
                            <a href="#" id="order_see_all" class="text-logoOrange">{{__('See all')}}</a>
                        </div>
                    </div>
                    <div class="row">
                        <div id="order_search_container" class="col-12 p-4"></div>
                    </div>
                </div>

                <div id="purchase_orders_search_block" class="pt-2 d-none search_result_box">
                    <div class="row">
                        <div class="col-6">
                            <h4>{{__('Purchase Orders')}}</h4>
                        </div>
                        <div class="col-6 text-right">
                            <a href="#" id="purchase_orders_see_all" class="text-logoOrange">{{__('See all')}}</a>
                        </div>
                    </div>
                    <div class="row">
                        <div id="purchase_orders_search_container" class="col-12 p-4"></div>
                    </div>
                </div>

                <div id="return_search_block" class="pt-2 d-none search_result_box">
                    <div class="row">
                        <div class="col-6">
                            <h4>{{__('Returns')}}</h4>
                        </div>
                        <div class="col-6 text-right">
                            <a href="#" id="return_see_all" class="text-logoOrange">{{__('See all')}}</a>
                        </div>
                    </div>
                    <div class="row">
                        <div id="return_search_container" class="col-12 p-4"></div>
                    </div>
                </div>
            </div>
            <div class="col-6 bg-secondary p-4">
                <div class="d-flex justify-content-end">
                    <i class="picon-close-circled-filled" title="Close" id="search_close"></i>
                </div>
                
                <div class="pb-4">
                    <h4 class="search_res_tab" rel="product" id="product_tab_open">{{__('Products')}}</h4>
                    <u id="product_search_total"></u>
                </div>

                <div class="pb-4">
                    <h4 class="search_res_tab" rel="purchase_orders" id="purchase_orders_tab_open">{{__('Purchase Orders')}}</h4>
                    <u id="purchase_orders_search_total"></u>
                </div>

                <div class="pb-4">
                    <h4 class="search_res_tab" rel="return" id="return_tab_open">{{__('Returns')}}</h4>
                    <u id="return_search_total"></u>
                </div>

                <div class="pb-4">
                    <h4 class="search_res_tab" rel="order" id="order_tab_open">{{__('Orders')}}</h4>
                    <u id="order_search_total"></u>
                </div>

            </div>
        </div>
    </div>
</div>
@push('js')
    <script>
        new SearchGlobal();
    </script>
@endpush
