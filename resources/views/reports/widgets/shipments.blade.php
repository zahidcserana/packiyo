<div class="row border-8 py-0 m-0 mb-5 bg-white actionsBlock w-100">
    <div class="border-right col-sm-2 py-md-4 text-center">
        <div id="total-shipments-container" class="font-weight-600 text-black">
            <i class="picon-inbox-light icon-lg pr-2"></i>{{ $shipmentCount }}
        </div>
        <div class="pt-2">{{ __('Total Shipments') }}</div>
    </div>
    <div class="border-right col-sm-2 py-md-4 text-center">
        <div id="total-items-shipped-container" class="font-weight-600 text-black">
            <i class="picon-tag-light icon-lg pr-2"></i>{{ $totalItemsShipped }}
        </div>
        <div class="pt-2">{{ __('Total items shipped') }}</div>
    </div>
    <div class="border-right col-sm-2 py-md-4 text-center">
        <div id="distinct-items-shipped-container" class="font-weight-600 text-black">
            <i class="picon-tag-light icon-lg pr-2"></i>{{ $distinctItemsShipped }}
        </div>
        <div class="pt-2">{{ __('Distinct items shipped') }}</div>
    </div>
</div>
