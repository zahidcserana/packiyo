@php
$purchaseOrdersMenuPageViewNames = (!empty($page) &&
    (
        in_array($page, [
            'purchase_orders.index',
            'purchase_orders.create',
            'purchase_orders.edit',
            'purchase_order.receive'
        ])
    )
);
@endphp
<li class="nav-item">
    <a class="nav-link" href="#purchase_orders" data-toggle="collapse" role="button" aria-expanded="{{ $purchaseOrdersMenuPageViewNames ? 'true' : 'false' }}" aria-controls="purchase_orders">
        <i class="picon-basket-light icon-lg"></i>
        <span class="nav-link-text">{{ __('Purchase Orders') }}</span>
    </a>
    <div class="collapse {{ $purchaseOrdersMenuPageViewNames ? 'show' : '' }}" id="purchase_orders">
        <ul class="nav nav-sm flex-column">
            @if (menu_item_visible('purchase_orders.index'))
            <li class="nav-item">
                <a href="{{ route('purchase_orders.index') }}" class="nav-link"><p class="{{ (!empty($page) && in_array($page, ['purchase_orders.index', 'purchase_order.receive'])) ? 'active_item' : '' }}">{{ __('Manage PO\'s') }}</p></a>
            </li>
            @endif
            @if (menu_item_visible('purchase_orders.create'))
            <li class="nav-item">
                <a href="{{ route('purchase_orders.create') }}" class="nav-link"><p>{{ __('Create Purchase Order') }}</p></a>
            </li>
            @endif
        </ul>
    </div>
</li>