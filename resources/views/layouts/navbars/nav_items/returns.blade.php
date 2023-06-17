@php
$returnsMenuPageViewNames = (!empty($page) &&
    (
        in_array($page, [
            'return.index',
            'return.create',
            'return.edit',
        ])
    )
);
@endphp
<li class="nav-item">
    <a class="nav-link" href="#returns" data-toggle="collapse" role="button" aria-expanded="{{ $returnsMenuPageViewNames ? 'true' : 'false' }}" aria-controls="returns">
        <i class="picon-undo-filled icon-lg"></i>
        <span class="nav-link-text">{{ __('Returns') }}</span>
    </a>
    <div class="collapse {{ $returnsMenuPageViewNames ? 'show' : '' }}" id="returns">
        <ul class="nav nav-sm flex-column">
            @if (menu_item_visible('return.index'))
            <li class="nav-item collapse-line">
                <a href="{{ route('return.index') }}" class="nav-link"><p class="{{ (! empty($page) && in_array($page, ['return.index', 'return.edit'])) ? 'active_item' : '' }}">{{ __('Manage Returns') }}</p></a>
            </li>
            @endif
            @if (menu_item_visible('return.create'))
            <li class="nav-item">
                <a href="{{ route('return.create') }}" class="nav-link openCreateModal"><p class="{{ (! empty($page) && in_array($page, ['return.create'])) ? 'active_item' : '' }}">{{ __('Create a Return') }}</p></a>
            </li>
            @endif
        </ul>
    </div>
</li>
