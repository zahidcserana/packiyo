@if(!empty($topSellingItems) && count($topSellingItems))
    @foreach($topSellingItems as $item)
        <div class="d-flex border-bottom-gray justify-content-between align-items-center py-2">
            <div class="d-flex align-items-center">
                @if (! empty($item->productImages) && count($item->productImages))
                    <img class="selling_widget_icon" src="{{ $item->productImages->first()->source }}" alt="">
                @else
                    <i class="picon-inbox-light icon-3xl" title="Edit"></i>
                @endif
                <div class="ml-3">
                        <span
                            class="font-sm font-weight-600 text-neutral-text-gray">{{ $item->name }}</span><br>
                    <a href="{{ route('product.edit', ['product' => $item->id]) }}" class="font-sm font-weight-600 text-black text-underline">{{ __('View') }}</a>
                </div>
            </div>
            <div class="d-flex justify-content-center flex-column align-content-end">
                <span class="text-neutral-text-gray font-sm font-weight-600 text-right">{{ __('SKU') }}</span>
                <span class="text-neutral-text-gray font-sm font-weight-600 text-right">{{ $item->sku }}</span>
            </div>
        </div>
    @endforeach
@endif
