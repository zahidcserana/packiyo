@php
$orderMenuOrderPageViewNames = (!empty($page) &&
    (
        in_array($page, [
            'order.index',
            'order.edit',
            'order.create'
        ])
    )
);
@endphp
<li class="nav-item">
    <a class="nav-link" href="#orders" data-toggle="collapse" role="button" aria-expanded="{{ $orderMenuOrderPageViewNames ? 'true' : 'false' }}" aria-controls="orders">
        <i class="picon-inbox-light icon-lg"></i>
        <span class="nav-link-text">{{ __('Orders') }}</span>
    </a>
    <div class="collapse {{ $orderMenuOrderPageViewNames ? 'show' : '' }}" id="orders">
        <ul class="nav nav-sm flex-column">
            @if (menu_item_visible('order.index'))
            <li class="nav-item collapse-line">
                <a href="{{ route('order.index') }}" class="nav-link"><p class="{{ (!empty($page) && in_array($page, ['order.index', 'order.edit'])) ? 'active_item' : '' }}">{{ __('Manage Orders') }}</p></a>
            </li>
            @endif
            @if (menu_item_visible('order.create'))
            <li class="nav-item collapse-line">
                <a href="{{ route('order.create') }}" class="nav-link"><p class="{{ (!empty($page) && $page === 'order.create') ? 'active_item' : '' }}">{{ __('Create an order') }}</p></a>
            </li>
            @endif
        </ul>
    </div>
</li>
