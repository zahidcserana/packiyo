<div class="position-fixed search-fixed d-md-none">
    <ul class="navbar-nav-custom align-items-center justify-content-center ml-auto ml-md-0 float-right">
        <li class="nav-item navbar-nav d-md-none">
            <a class="d-flex align-items-center justify-content-center nav-link" href="#" data-action="search-show" data-target="#navbar-search-main">
                <img src="{{ asset('img/search_light.svg') }}" alt="Search">
            </a>
        </li>
    </ul>
    <nav class="navbar navbar-top navbar-expand d-md-none p-0">
        <!-- Search form -->
        <form class="navbar-search {{ $searchClass ?? 'navbar-search-light' }} form-inline mr-sm-3" id="navbar-search-main" method="post" action="{{route('search.form')}}">
            @csrf
            <div class="form-group mb-0">
                <div class="input-group input-group-alternative input-group-merge bg-lightGrey font-sm " id="search_input_container">
                    <input id="global_search_input" value="{{isset($keyword) ? $keyword : ''}}" name="keyword" required class="form-control font-sm font-weight-600 text-neutral-gray " placeholder="{{ __('Global search') }}" type="text" autofocus/>
                    <div class="input-group-append align-items-center">
                            <span class="input-group-text"><img src="{{ asset('img/search.svg') }}" alt=""></span>

                    </div>
                </div>
            </div>
            <button type="button" class="close" data-action="search-close" data-target="#navbar-search-main" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </form>
    </nav>
</div>

