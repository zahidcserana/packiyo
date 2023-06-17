<nav class="navbar navbar-top navbar-expand navbar-top-main border-bottom {{ $navClass ?? 'navbar-light bg-white' }} p-md-3 p-lg-3">
    <div class="container-fluid">
        <div class="collapse navbar-collapse search_top_container" id="navbarSupportedContent">
            <form class="navbar-search {{ $searchClass ?? 'navbar-search-light' }} form-inline mr-sm-3" id="navbar-search-main" method="post" action="{{route('search.form')}}">
                @csrf
                <div class="form-group mb-0">
                    <div class="input-group input-group-alternative input-group-merge bg-lightGrey font-sm " id="search_input_container">
                        {{--<div class="input-group-prepend">
                            <span class="input-group-text"><img src="{{ asset('img/search.svg') }}" alt=""></span>
                        </div>--}}
                        <input id="global_search_input" value="{{isset($keyword) ? $keyword : ''}}" name="keyword" required class="form-control font-sm font-weight-600 text-neutral-gray " placeholder="{{ __('Global search') }}" type="text" />
                        <div class="input-group-append align-items-center">
                            {{--<button class="command-button">--}}
                                <span class="input-group-text"><img src="{{ asset('img/search.svg') }}" alt=""></span>
                                {{--<span class="input-group-text p-0 text-black font-weight-600"><img width="13px" src="{{ asset('img/command.svg') }}" alt="">&nbsp;&nbsp;F</span>--}}
                            {{--</button>--}}

                        </div>
                    </div>
                </div>
                <button type="button" class="close" data-action="search-close" data-target="#navbar-search-main" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </form>
            <!-- Navbar links -->
            <ul class="navbar-nav-custom align-items-center mb-md-0 ml-md-auto">
                <li class="nav-item d-xl-none">
                <!-- Sidenav toggler -->
                    <div class="pr-3 sidenav-toggler sidenav-toggler-light" data-action="sidenav-pin" data-target="#sidenav-main">
                        <div class="sidenav-toggler-inner">
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                        </div>
                    </div>
                </li>
<!--                <li class="nav-item dropdown">
                    <a class="nav-link text-black" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="position-relative">
                            <img src="{{ asset('img/notification.svg') }}" alt="">
                            <sup class="position-absolute active-notify"></sup>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-xl dropdown-menu-right py-0 overflow-hidden">
                        &lt;!&ndash; Dropdown header &ndash;&gt;
                        <div class="px-3 py-3">
                            <h6 class="text-muted m-0">You have <strong class="text-primary">13</strong> notifications.</h6>
                        </div>
                        &lt;!&ndash; List group &ndash;&gt;
                        <div class="list-group list-group-flush">
                            <a href="#!" class="list-group-item list-group-item-action">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        &lt;!&ndash; Avatar &ndash;&gt;
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-1.jpg" class="avatar rounded-circle">
                                    </div>
                                    <div class="col ml&#45;&#45;2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h4 class="mb-0">John Snow</h4>
                                            </div>
                                            <div class="text-right text-muted">
                                                <small>2 hrs ago</small>
                                            </div>
                                        </div>
                                        <p class="mb-0">Let's meet at Starbucks at 11:30. Wdyt?</p>
                                    </div>
                                </div>
                            </a>
                            <a href="#!" class="list-group-item list-group-item-action">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        &lt;!&ndash; Avatar &ndash;&gt;
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-2.jpg" class="avatar rounded-circle">
                                    </div>
                                    <div class="col ml&#45;&#45;2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h4 class="mb-0">John Snow</h4>
                                            </div>
                                            <div class="text-right text-muted">
                                                <small>3 hrs ago</small>
                                            </div>
                                        </div>
                                        <p class="b-0">A new issue has been reported for Argon.</p>
                                    </div>
                                </div>
                            </a>
                            <a href="#!" class="list-group-item list-group-item-action">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        &lt;!&ndash; Avatar &ndash;&gt;
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-3.jpg" class="avatar rounded-circle">
                                    </div>
                                    <div class="col ml&#45;&#45;2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h4 class="mb-0">John Snow</h4>
                                            </div>
                                            <div class="text-right text-muted">
                                                <small>5 hrs ago</small>
                                            </div>
                                        </div>
                                        <p class="mb-0">Your posts have been liked a lot.</p>
                                    </div>
                                </div>
                            </a>
                            <a href="#!" class="list-group-item list-group-item-action">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        &lt;!&ndash; Avatar &ndash;&gt;
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-4.jpg" class="avatar rounded-circle">
                                    </div>
                                    <div class="col ml&#45;&#45;2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h4 class="mb-0">John Snow</h4>
                                            </div>
                                            <div class="text-right text-muted">
                                                <small>2 hrs ago</small>
                                            </div>
                                        </div>
                                        <p class="mb-0">Let's meet at Starbucks at 11:30. Wdyt?</p>
                                    </div>
                                </div>
                            </a>
                            <a href="#!" class="list-group-item list-group-item-action">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        &lt;!&ndash; Avatar &ndash;&gt;
                                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-5.jpg" class="avatar rounded-circle">
                                    </div>
                                    <div class="col ml&#45;&#45;2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h4 class="mb-0">John Snow</h4>
                                            </div>
                                            <div class="text-right text-muted">
                                                <small>3 hrs ago</small>
                                            </div>
                                        </div>
                                        <p class="mb-0">A new issue has been reported for Argon.</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        &lt;!&ndash; View all &ndash;&gt;
                        <a href="#!" class="dropdown-item text-center text-primary font-weight-bold py-3">View all</a>
                    </div>
                </li>-->
{{--                <li class="nav-item dropdown">--}}
{{--                    <a class="nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">--}}
{{--                        <i class="ni ni-ungroup"></i>--}}
{{--                    </a>--}}
{{--                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-light bg-default dropdown-menu-right">--}}
{{--                        <div class="row shortcuts px-4">--}}
{{--                            <a href="#!" class="col-4 shortcut-item">--}}
{{--                                <span class="shortcut-media avatar rounded-circle bg-gradient-red">--}}
{{--                                    <i class="ni ni-calendar-grid-58"></i>--}}
{{--                                </span>--}}
{{--                                <small>Calendar</small>--}}
{{--                            </a>--}}
{{--                            <a href="#!" class="col-4 shortcut-item">--}}
{{--                                <span class="shortcut-media avatar rounded-circle bg-gradient-orange">--}}
{{--                                    <i class="ni ni-email-83"></i>--}}
{{--                                </span>--}}
{{--                                <small>Email</small>--}}
{{--                            </a>--}}
{{--                            <a href="#!" class="col-4 shortcut-item">--}}
{{--                                <span class="shortcut-media avatar rounded-circle bg-gradient-info">--}}
{{--                                    <i class="ni ni-credit-card"></i>--}}
{{--                                </span>--}}
{{--                                <small>Payments</small>--}}
{{--                            </a>--}}
{{--                            <a href="#!" class="col-4 shortcut-item">--}}
{{--                                <span class="shortcut-media avatar rounded-circle bg-gradient-green">--}}
{{--                                    <i class="ni ni-books"></i>--}}
{{--                                </span>--}}
{{--                                <small>Reports</small>--}}
{{--                            </a>--}}
{{--                            <a href="#!" class="col-4 shortcut-item">--}}
{{--                                <span class="shortcut-media avatar rounded-circle bg-gradient-purple">--}}
{{--                                    <i class="ni ni-pin-3"></i>--}}
{{--                                </span>--}}
{{--                                <small>Maps</small>--}}
{{--                            </a>--}}
{{--                            <a href="#!" class="col-4 shortcut-item">--}}
{{--                                <span class="shortcut-media avatar rounded-circle bg-gradient-yellow">--}}
{{--                                    <i class="ni ni-basket"></i>--}}
{{--                                </span>--}}
{{--                                <small>Shop</small>--}}
{{--                            </a>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </li>--}}
{{--                @include('shared.forms.dropdowns.dropdown')--}}
            </ul>
            <div class="navbar-header-logo">
                <a class="navbar-brand" href="{{ route('home') }}">
                    <img width="120" class="mh-100" src="{{ asset('img/packiyo-logo-on-transparent.png') }}" alt="">
                </a>
            </div>
            @include('shared.forms.dropdowns.dropdown')
            <ul class="navbar-nav-custom align-items-center mb-md-0 ml-auto ml-md-0">
                <li class="nav-item dropdown">
                    <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="media align-items-center">
                            <div class="media-body  d-none d-lg-block text-black">
                                <div class="mb-2 font-weight-600">{{ auth()->user()->contactInformation->name }}</div>
                                <div class="mb-0 text-xs font-weight-400">{{ auth()->user()->email }}</div>
                            </div>
                            <span class="avatar ml-2 avatar-custom rounded-circle">
                                <img alt="Image placeholder" src="{{ auth()->user()->profilePicture() }}" class="{{ auth()->user()->picture == '' ? 'd-none' : '' }}">
                            </span>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">{{ __('Welcome!') }}</h6>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="dropdown-item">
                            <i class="ni ni-single-02"></i>
                            <span>{{ __('My profile') }}</span>
                        </a>
                        <a href="{{ route('account.settings') }}" class="dropdown-item">
                            <i class="ni ni-settings-gear-65"></i>
                            <span>{{ __('Account Settings') }}</span>
                        </a>
                        <a href="{{ route('profile.activity') }}" class="dropdown-item">
                            <i class="ni ni-calendar-grid-58"></i>
                            <span>{{ __('Activity') }}</span>
                        </a>
                        <a href="#!" class="dropdown-item">
                            <i class="ni ni-support-16"></i>
                            <span>{{ __('Support') }}</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
                            <i class="ni ni-user-run"></i>
                            <span>{{ __('Logout') }}</span>
                        </a>
                    </div>
                </li>
            </ul>
            @include('shared.search.results')
        </div>

    </div>
</nav>
