@php
$shipmentMenuPageViewNames = (!empty($page) &&
    (
        in_array($page, [
            'shipment.index',
            'shipment.create',
            'shipment.edit',
        ])
    )
);
@endphp
<li class="nav-item">
    <a class="nav-link" href="#shipments" data-toggle="collapse" role="button" aria-expanded="{{ $shipmentMenuPageViewNames ? 'true' : 'false' }}" aria-controls="warehouses">
        <i class="picon-truck-light icon-lg"></i>
        <span class="nav-link-text">{{ __('Shipments') }}</span>
    </a>
    <div class="collapse {{ $shipmentMenuPageViewNames ? 'show' : '' }}" id="shipments">
        <ul class="nav nav-sm flex-column">
            @if (menu_item_visible('shipment.index'))
            <li class="nav-item collapse-line">
                <a href="{{ route('shipment.index') }}" class="nav-link"><p class="{{ (!empty($page) && in_array($page, ['shipment.index', 'shipment.create', 'shipment.edit'])) ? 'active_item' : '' }}">{{ __('All Shipments') }}</p></a>
            </li>
            @endif
        </ul>
    </div>
</li>
