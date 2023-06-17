<div class="tab-pane fade show active" id="orders-tab-content" role="tabpanel" aria-labelledby="orders-tab">
    <div class="d-lg-flex justify-content-between">
        <div class="w-100">
            <div class="d-flex justify-content-between mr-lg-3 border-bottom py-3">
                <span class="text-neutral-text-gray font-sm font-weight-600">{{ __('Orders Today') }}</span>
                <span class="text-black text-right font-sm font-weight-600">{{ $data->orders->ordersToday ?? 0 }}</span>
            </div>
            <div class="d-flex justify-content-between mr-lg-3 border-bottom py-3">
                <span class="text-neutral-text-gray font-sm font-weight-600">{{ __('Orders Confirm') }}</span>
                <span class="text-black text-right font-sm font-weight-600">{{ $data->orders->ordersConfirm ?? 0 }}</span>
            </div>
            <div class="d-flex justify-content-between mr-lg-3 border-bottom py-3">
                <span class="text-neutral-text-gray font-sm font-weight-600">{{ __('Orders To Ship') }}</span>
                <span class="text-black text-right font-sm font-weight-600">{{ $data->orders->ordersToShip ?? 0 }}</span>
            </div>
            <div class="d-flex justify-content-between mr-lg-3 border-bottom py-3">
                <span class="text-neutral-text-gray font-sm font-weight-600">{{ __('Orders Complete') }}</span>
                <span class="text-black text-right font-sm font-weight-600">{{ $data->orders->ordersComplete ?? 0 }}</span>
            </div>
        </div>
    </div>
</div>
<div class="tab-pane fade" id="shipments-tab-content" role="tabpanel" aria-labelledby="shipments-tab">
    <div class="d-lg-flex justify-content-between">
        <div class="w-100">
            <div class="d-flex justify-content-between mr-lg-3 border-bottom py-3">
                <span class="text-neutral-text-gray font-sm font-weight-600">{{ __('Today') }}</span>
                <span class="text-black text-right font-sm font-weight-600">{{ $data->shipments->shipmentsToday }}</span>
            </div>
            <div class="d-flex justify-content-between mr-lg-3 border-bottom py-3">
                <span class="text-neutral-text-gray font-sm font-weight-600">{{ __('Yesterday') }}</span>
                <span class="text-black text-right font-sm font-weight-600">{{ $data->shipments->shipmentsYesterday }}</span>
            </div>
            <div class="d-flex justify-content-between mr-lg-3 border-bottom py-3">
                <span class="text-neutral-text-gray font-sm font-weight-600">{{ __('Last 7 days') }}</span>
                <span class="text-black text-right font-sm font-weight-600">{{ $data->shipments->shipmentsLastWeek }}</span>
            </div>
            <div class="d-flex justify-content-between mr-lg-3 border-bottom py-3">
                <span class="text-neutral-text-gray font-sm font-weight-600">{{ __('Last Month') }}</span>
                <span class="text-black text-right font-sm font-weight-600">{{ $data->shipments->shipmentsLastMonth }}</span>
            </div>
        </div>
    </div>
</div>
<div class="tab-pane fade" id="products-tab-content" role="tabpanel" aria-labelledby="products-tab">
    <div class="d-lg-flex justify-content-between">
        <div class="w-100">
            <div class="d-flex justify-content-between mr-lg-3 border-bottom py-3">
                <span class="text-neutral-text-gray font-sm font-weight-600">{{ __('Unique Orders') }}</span>
                <span class="text-black text-right font-sm font-weight-600">{{ $data->products->productUniqueOrders }}</span>
            </div>
            <div class="d-flex justify-content-between mr-lg-3 border-bottom py-3">
                <span class="text-neutral-text-gray font-sm font-weight-600">{{ __("Back orders SKU's") }}</span>
                <span class="text-black text-right font-sm font-weight-600">{{ $data->products->productBackordered }}</span>
            </div>
            <div class="d-flex justify-content-between mr-lg-3 border-bottom py-3">
                <span class="text-neutral-text-gray font-sm font-weight-600">{{ __('Pieces') }}</span>
                <span class="text-black text-right font-sm font-weight-600">{{ $data->products->productPieces }}</span>
            </div>
            <div class="d-flex justify-content-between mr-lg-3 border-bottom py-3">
                <span class="text-neutral-text-gray font-sm font-weight-600">{{ __("Unique SKU's") }}</span>
                <span class="text-black text-right font-sm font-weight-600">{{ $data->products->productUniqueSkus }}</span>
            </div>
        </div>
    </div>
</div>
<div class="tab-pane fade" id="purchases-tab-content" role="tabpanel" aria-labelledby="purchases-tab">
    <div class="d-lg-flex justify-content-between">
        <div class="w-100">
            <div class="d-flex justify-content-between mr-lg-3 border-bottom py-3">
                <span class="text-neutral-text-gray font-sm font-weight-600">{{ __('Open') }}</span>
                <span class="text-black text-right font-sm font-weight-600">{{ $data->purchases->purchasesOpen }}</span>
            </div>
            <div class="d-flex justify-content-between mr-lg-3 border-bottom py-3">
                <span class="text-neutral-text-gray font-sm font-weight-600">{{ __('Complete') }}</span>
                <span class="text-black text-right font-sm font-weight-600">{{ $data->purchases->purchasesComplete }}</span>
            </div>
            <div class="d-flex justify-content-between mr-lg-3 border-bottom py-3">
                <span class="text-neutral-text-gray font-sm font-weight-600">{{ __('Open Items') }}</span>
                <span class="text-black text-right font-sm font-weight-600">{{ $data->purchases->purchasesOpenItems }}</span>
            </div>
            <div class="d-flex justify-content-between mr-lg-3 border-bottom py-3">
                <span class="text-neutral-text-gray font-sm font-weight-600">{{ __('Completed Item') }}</span>
                <span class="text-black text-right font-sm font-weight-600">{{ $data->purchases->purchasesCompletedItems }}</span>
            </div>
        </div>
    </div>
</div>
