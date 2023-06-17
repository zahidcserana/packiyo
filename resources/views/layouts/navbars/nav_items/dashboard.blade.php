@if (menu_item_visible('home'))
<li class="nav-item">
    <a href="{{ route('home') }}" class="nav-link"  aria-expanded="{{ (! empty($page) && $page === 'home') ? 'true' : 'false' }}">
        <i class="picon-category-light icon-lg"></i>
        <span class="nav-link-text">{{ __('Dashboard') }}</span>
    </a>
</li>
@endif
