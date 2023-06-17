@php
$settingMenuPageViewNames = (!empty($page) &&
    (
        in_array($page, [
            'user_settings.edit',
            'settings.manageUsers',
            'order_status.index',
            'order_status.edit',
            'order_status.create',
            'location_type.index',
            'location_type.create',
            'shipping_box.index',
            'shipping_box.create',
            'shipping_box.edit',
            'picking_carts.index',
            'picking_carts.create',
            'picking_carts.edit',
            'tote.index',
            'tote.create',
            'tote.edit',
            'shipping_method_mapping.index',
            'shipping_method_mapping.create',
            'shipping_method_mapping.edit',
            'shipping_method.index',
            'shipping_method.edit',
            'location.index',
            'location.create',
            'location.edit',
            'return_status.index',
            'supplier.index',
            'supplier.edit'
        ])
    )
);
@endphp
<li class="nav-item">
    <div class="dropup dropup-right position-fixed">
        <a href="#" class="nav-link dropdown-toggle {{ $settingMenuPageViewNames ? 'active_button' : '' }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="picon-settings-light icon-lg"></i>
        </a>
        <div class="dropdown-menu position-absolute">
            <ul class="p-0">
                @if (menu_item_visible('user_settings.edit'))
                <li class="nav-item">
                    <a href="{{ route('user_settings.edit') }}" class="dropdown-item {{ (!empty($page) && $page === 'user_settings.edit') ? 'active_item' : '' }}"><span class="nav-link-text">{{ __('General') }}</span></a>
                </li>
                @endif
                @if (menu_item_visible('settings.manageUsers'))
                <li class="nav-item">
                    <a href="{{ route('settings.manageUsers') }}" class="dropdown-item {{ (!empty($page) && $page === 'settings.manageUsers') ? 'active_item' : '' }}"><span class="nav-link-text">{{ __('Manage Users') }}</span></a>
                </li>
                @endif
                <li class="nav-item">
                    <a href="#" class="dropdown-item"><span class="nav-link-text">{{ __('Manage Carriers') }}</span></a>
                </li>
                <li class="nav-item">
                    <a href="#" class="dropdown-item"><span class="nav-link-text">{{ __('Manage Stores') }}</span></a>
                </li>
                <li class="nav-item">
                    <a href="#" class="dropdown-item"><span class="nav-link-text">{{ __('Notifications') }}</span></a>
                </li>
                @if (menu_item_visible('location_type.index'))
                <li class="nav-item">
                    <a href="{{ route('location_type.index') }}" class="dropdown-item {{ (! empty($page) && in_array($page, ['location_type.index', 'location_type.create'])) ? 'active_item' : '' }}"><span class="nav-link-text">{{ __('Location Types') }}</span></a>
                </li>
                @endif
                @if (menu_item_visible('order_status.index'))
                <li class="nav-item">
                    <a href="{{ route('order_status.index') }}" class="dropdown-item {{ (! empty($page) && in_array($page, ['order_status.index', 'order_status.edit', 'order_status.create'])) ? 'active_item' : '' }}"><span class="nav-link-text">{{ __('Order Statuses') }}</span></a>
                </li>
                @endif
                @if (menu_item_visible('shipping_method_mapping.index'))
                <li class="nav-item">
                    <a href="{{ route('shipping_method_mapping.index') }}" class="dropdown-item {{ (!empty($page) && in_array($page, ['shipping_method_mapping.index', 'shipping_method_mapping.create', 'shipping_method_mapping.edit'])) ? 'active_item' : '' }}"><span class="nav-link-text">{{ __('Shipping Method Mapping') }}</span></a>
                </li>
                @endif
                @if (menu_item_visible('shipping_method.index'))
                <li class="nav-item">
                    <a href="{{ route('shipping_method.index') }}" class="dropdown-item {{ (!empty($page) && in_array($page, ['shipping_method.index', 'shipping_method.edit'])) ? 'active_item' : '' }}"><span class="nav-link-text">{{ __('Shipping Method') }}</span></a>
                </li>
                @endif
                @if (menu_item_visible('shipping_box-picking_carts-tote-location'))
                <li class="nav-item">
                    <a href="{{ route('shipping_box.index') }}" class="dropdown-item {{ (!empty($page) && in_array($page, ['shipping_box.index', 'shipping_box.create', 'shipping_box.edit'])) ? 'active_item' : '' }}"><span class="nav-link-text">{{ __('Shipment Boxes') }}</span></a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('picking_carts.index') }}" class="dropdown-item {{ (!empty($page) && in_array($page, ['picking_carts.index', 'picking_carts.create', 'picking_carts.edit'])) ? 'active_item' : '' }}"><span class="nav-link-text">{{ __('Picking Carts') }}</span></a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('tote.index') }}" class="dropdown-item {{ (!empty($page) && in_array($page, ['tote.index', 'tote.create', 'tote.edit'])) ? 'active_item' : '' }}"><span class="nav-link-text">{{ __('Picking Totes') }}</span></a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('location.index') }}" class="dropdown-item {{ (! empty($page) && in_array($page, ['location.index', 'location.create', 'location.edit'])) ? 'active_item' : '' }} openCreateModal"><span class="nav-link-text">{{ __('Locations') }}</span></a>
                </li>
                @endif
                @if (menu_item_visible('return_status.index'))
                <li class="nav-item">
                    <a href="{{ route('return_status.index') }}" class="dropdown-item {{ (! empty($page) && in_array($page, ['return_status.index'])) ? 'active_item' : '' }}"><span class="nav-link-text">{{ __('Return Statuses') }}</span></a>
                </li>
                @endif
                @if (menu_item_visible('supplier.index'))
                <li class="nav-item">
                    <a href="{{ route('supplier.index') }}" class="dropdown-item {{ (! empty($page) && in_array($page, ['supplier.index'])) ? 'active_item' : '' }}"><span class="nav-link-text">{{ __('Manage Vendors') }}</span></a>
                </li>
                @endif
                @if (menu_item_visible('supplier.create'))
                <li class="nav-item">
                    <a href="{{ route('supplier.index') }}#open-modal" class="dropdown-item {{ (! empty($page) && in_array($page, ['supplier.edit'])) ? 'active_item' : '' }}"><span class="nav-link-text">{{ __('Add Vendor') }}</span></a>
                </li>
                @endif
                @if (menu_item_visible('printer.index'))
                <li class="nav-item">
                    <a href="{{ route('printer.index') }}" class="dropdown-item"><span class="nav-link-text">{{ __('Printers') }}</span></a>
                </li>
                @endif
            </ul>
        </div>
    </div>
</li>
