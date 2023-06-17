<div class="header bg-lightGrey py-2 py-md-3">
    <div class="container-fluid">
        <div class="row {{ isset($tabs) ? 'with-tabs' : '' }}">
            <div class="{{ isset($button['title']) ? 'col-6' : 'col-12' }} col-lg-6">
                <div class="header-body text-black">
                    <span class="font-weight-600 font-text-lg mr--2">
                        {!! ($title ?? '') . (! empty($subtitle) || isset($tabs) ? '/' : '') !!}
                    </span>
                    <span class="font-weight-400 font-md">
                        {{ $subtitle ?? (isset($tabs) ? collect($tabs)->where('route', 'current')->first()['name'] : '') }}
                    </span>
                </div>
            </div>
            <div class="col-6 col-lg-6 d-flex justify-content-end align-items-center">
                @if (isset($button['title']))
                    <a
                        href="{{ $button['href'] ?? '#' }}"
                        class="btn bg-logoOrange px-lg-5 text-white float-right"
                        @if (isset($button['data-toggle'], $button['data-target']))
                            data-toggle="{{ $button['data-toggle'] }}"
                            data-target="{{ $button['data-target'] }}"
                        @endif
                    >
                        {{ $button['title'] }}
                    </a>
                @elseif (isset($tabs))
                    <ul class="nav">
                        @foreach($tabs as $tab)
                            <li class="nav-item {{ $tab['route'] === 'current' ? 'active' : '' }}">
                                <a
                                    class="nav-link font-weight-600 font-sm text-black"
                                    aria-current="page"
                                    href="{{ $tab['route'] === 'current' ? '#' : $tab['route'] }}"
                                >
                                    {{ $tab['name'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <a
                    href="#"
                    id="bulk-edit-btn"
                    class="btn bg-logoOrange mx-1 px-lg-5 text-white float-right"
                    data-toggle="modal"
                    data-target="#bulk-edit-modal"
                    hidden
                >
                    {{ __('Bulk Edit') }}
                </a>
            </div>
        </div>
    </div>
</div>
