@if (menu_item_visible('packing.index'))
    <li class="nav-item">
        <a class="nav-link" href="{{ route('packing.index') }}" aria-expanded="{{ (! empty($page) && $page === 'packing.index') ? 'true' : 'false' }}">
            <i class="picon-archive-light icon-lg bg-logoOrange rounded icon-background text-white"></i>
            <span class="nav-link-text">{{ __('Pack Orders') }}</span>
        </a>
    </li>
@endif
