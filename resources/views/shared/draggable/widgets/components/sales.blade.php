<div class="d-flex border-bottom-gray justify-content-between py-3">
    <div>
        <span class="font-xs font-weight-600 text-neutral-text-gray">{{ __('Approx Revenue') }}</span>
    </div>
    <div class="d-flex justify-content-end">
        <span class="text-black font-xs font-weight-600">${{ $data->ordersTotalPrice }}</span>
        {{--                <span class="success-bg success-text font-xxs increment-box">+ 7.89 %</span>--}}
    </div>
</div>
<div class="d-flex border-bottom-gray justify-content-between py-3">
    <div>
        <span class="font-xs font-weight-600 text-neutral-text-gray">{{ __('Units Sold') }}</span>
    </div>
    <div class="d-flex justify-content-end">
        <span class="text-black font-xs font-weight-600">{{ $data->unitsSold }}</span>
    </div>
</div>
<div class="d-flex border-bottom-gray justify-content-between py-3">
    <div>
        <span class="font-xs font-weight-600 text-neutral-text-gray">{{ __('Total Orders') }}</span>
    </div>
    <div class="d-flex justify-content-end">
        <span class="text-black font-xs font-weight-600">{{ $data->totalOrders }}</span>
    </div>
</div>
<div class="d-flex border-bottom-gray justify-content-between py-3">
    <div>
        <span class="font-xs font-weight-600 text-neutral-text-gray">{{ __('Avg. Order Size') }}</span>
    </div>
    <div class="d-flex justify-content-end">
        <span class="text-black font-xs font-weight-600">${{ $data->avgOrderSize ?? 0 }}</span>
    </div>
</div>
