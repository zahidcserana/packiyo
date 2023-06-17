@php
    $inventoryMenuPageViewNames = (!empty($page) &&
    (
        in_array($page, [
            'warehouses.index',
            'warehouses.create',
            'warehouses.edit',
            'product.index',
            'product.edit',
            'lot.index',
            'lot.edit',
            'inventory_log.index',
            'productLocation.index'
        ])
    )
);
@endphp
<li class="nav-item">
    <a href="#inventory" class="nav-link"  data-toggle="collapse" role="button" aria-expanded="{{ $inventoryMenuPageViewNames ? 'true' : 'false' }}" aria-controls="inventory">
        <i class="picon-box-light icon-lg"></i>
        <span class="nav-link-text">{{ __('Inventory') }}</span>
    </a>
    <div class="collapse {{ $inventoryMenuPageViewNames ? 'show' : '' }}" id="inventory">
        <ul class="nav nav-sm flex-column">
            @if (menu_item_visible('product.index'))
            <li class="nav-item">
                <a href="{{ route('product.index') }}" class="nav-link"><p class="{{ (! empty($page) && in_array($page, ['product.index', 'product.edit'])) ? 'active_item' : '' }}">{{ __('Products') }}</p></a>
            </li>
            @endif
            @if (menu_item_visible('product.create'))
            <li class="nav-item">
                <a href="{{ route('product.index') }}#open-modal" class="nav-link openCreateModal"><p>{{ __('Create Product') }}</p></a>
            </li>
            @endif
            @if (menu_item_visible('warehouses-productLocation-lot'))
            <li class="nav-item">
                <a href="{{ route('warehouses.index') }}" class="nav-link openCreateModal"><p class="{{ (! empty($page) && in_array($page, ['warehouses.index', 'warehouses.create', 'warehouses.edit'])) ? 'active_item' : '' }}">{{ __('Warehouses') }}</p></a>
            </li>
            <li class="nav-item">
                <a href="{{ route('productLocation.index') }}" class="nav-link openCreateModal"><p class="{{ (! empty($page) && in_array($page, ['productLocation.index'])) ? 'active_item' : '' }}">{{ __('Product location') }}</p></a>
            </li>
            <li class="nav-item">
                <a href="{{ route('lot.index') }}" class="nav-link openCreateModal"><p class="{{ (! empty($page) && in_array($page, ['lot.index'])) ? 'active_item' : '' }}">{{ __('Lots') }}</p></a>
            </li>
            @endif
            @if (menu_item_visible('inventory_log.index'))
            <li class="nav-item">
                <a href="{{ route('inventory_log.index') }}" class="nav-link"><p class="{{ (! empty($page) && in_array($page, ['inventory_log.index'])) ? 'active_item' : '' }}">{{ __('Change Log') }}</p></a>
            </li>
            @endif
        </ul>
    </div>
</li>
