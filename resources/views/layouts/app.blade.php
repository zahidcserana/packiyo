<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>
        <!-- Favicon -->
        <link href="{{ asset('favicon.ico') }}" rel="icon" type="image/png">
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
        <!-- Icons -->
        <link href="{{ asset('argon') }}/vendor/nucleo/css/nucleo.css" rel="stylesheet">
        <link href="{{ asset('argon') }}/vendor/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('argon') }}/vendor/select2/dist/css/select2.min.css">
        <link rel="stylesheet" href="{{ asset('argon') }}/vendor/datatables.net-bs4/css/dataTables.bootstrap4.min.css">
        <link rel="stylesheet" href="{{ asset('argon') }}/vendor/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css">
        <link rel="stylesheet" href="{{ asset('argon') }}/vendor/datatables.net-select-bs4/css/select.bootstrap4.min.css">
        <link rel="stylesheet" href="{{ asset('argon') }}/vendor/datepicker/daterangepicker.css">
        <link rel="stylesheet" href="{{ asset('argon') }}/vendor/dropzone/dist/min/dropzone.min.css">

        <link href="{{ asset('argon') }}/fonts/automagical/style.css" rel="stylesheet">
        @stack('css')

        <link type="text/css" href="{{ mix('css/app.css') }}" rel="stylesheet">

        @if ($customCSS)
            <style>
                {!! $customCSS !!}
            </style>
        @endif
    </head>
    <body class="{{ $class ?? '' }} {{ \Illuminate\Support\Arr::get($_COOKIE, 'sidenav-state') === 'pinned' ? 'g-sidenav-show g-sidenav-pinned' : '' }}">

        @auth()
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            @if (!in_array(request()->route()->getName(), ['welcome', 'page.pricing', 'page.lock']))
                @include('layouts.navbars.sidebar')
            @endif
        @endauth

        <div class="main-content d-flex flex-column bg-lightGrey h-100 overflow-auto">
            @include('layouts.navbars.navbar')
            @yield('content')
        </div>

{{--        @if(!auth()->check() || in_array(request()->route()->getName(), ['welcome', 'page.pricing', 'page.lock']))--}}
{{--            @include('layouts.footers.guest')--}}
{{--        @endif--}}

        @include('shared.modals.alert')
        @include('shared.modals.confirm')
        @include('shared.modals.multipleLogin')
        @include('shared.modals.orderCancellation')
        @include('shared.modals.orderItemCancellation')
        @include('shared.modals.orderMarkAsFulfilled')
        @include('shared.modals.passwordResetSuccess')
        @auth()
            @if (!in_array(request()->route()->getName(), ['welcome', 'page.pricing', 'page.lock']))
                @include('shared.search.fixed')
            @endif
        @endauth

        <!-- Argon JS -->
        <script src="{{ mix('js/app.js') }}"></script>
        <script src="{{ mix('js/_widgets.js') }}"></script>

        <script>
            window.app.data = {
                currency: '{{ $sessionCustomer->currency ?? '' }}',
                countries: @json(Webpatser\Countries\Countries::select(['id', 'name AS text', 'iso_3166_2 AS country_code'])->get())
            }
        </script>

        <x-toastr />
        <x-print_modal />

            {{--        <script src="{{ asset('argon') }}/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>--}}
        <script src="{{ asset('argon') }}/vendor/js-cookie/js.cookie.js"></script>
        <script src="{{ asset('argon') }}/vendor/jquery.scrollbar/jquery.scrollbar.min.js"></script>
        <script src="{{ asset('argon') }}/vendor/jquery-scroll-lock/dist/jquery-scrollLock.min.js"></script>
        <script src="{{ asset('argon') }}/vendor/lavalamp/js/jquery.lavalamp.min.js"></script>
        <script src="{{ asset('argon') }}/vendor/select2/dist/js/select2.min.js"></script>

        <!-- Optional JS -->
        <script src="{{ asset('argon') }}/vendor/moment/moment.js"></script>
        <script src="{{ asset('argon') }}/vendor/moment/moment-timezone.js"></script>
        <script src="{{ asset('argon') }}/vendor/datepicker/daterangepicker.js"></script>
        <script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.min.js"></script>
        <script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.extension.js"></script>
        <script src="{{ asset('argon') }}/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="{{ asset('argon') }}/vendor/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
        <script src="{{ asset('argon') }}/vendor/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
        <script src="{{ asset('argon') }}/vendor/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
        <script src="{{ asset('argon') }}/vendor/datatables.net-buttons/js/buttons.html5.min.js"></script>
        <script src="{{ asset('argon') }}/vendor/datatables.net-buttons/js/buttons.flash.min.js"></script>
        <script src="{{ asset('argon') }}/vendor/datatables.net-buttons/js/buttons.print.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
        <script src="{{ asset('argon') }}/vendor/datatables.net-buttons/js/buttons.colVis.min.js"></script>
        <script src="{{ asset('argon') }}/vendor/dropzone/dist/min/dropzone.min.js"></script>
        {{--        <script src="{{ asset('argon') }}/vendor/datatables.net-select/js/dataTables.select.min.js"></script>--}}

        <script src="{{ asset('argon') }}/js/argon.js?v=1.0.0"></script>
        <script src="{{ asset('argon') }}/js/demo.min.js"></script>
        <script src="https://unpkg.com/@google/markerclustererplus@4.0.1/dist/markerclustererplus.min.js"></script>
        @if (session()->has('multiple_login'))
            <script>
                $('#multipleLoginModal').modal('show');
            </script>
        @endif
        @stack('modal')

        @stack('js')
        @stack('widget-js')

        @auth
            @if (config('intercom.api_base') && config('intercom.app_id'))
                <script>
                    window.intercomSettings = {
                        api_base: "{{ config('intercom.api_base') }}",
                        app_id: "{{ config('intercom.app_id') }}",
                        name: "{{ auth()->user()->contactInformation->name }}",
                        email: "{{ auth()->user()->email }}",
                        created_at: "{{ auth()->user()->created_at }}",
                        user_hash: "{{ auth()->user()->userHash() }}"
                    };
                </script>

                <script>
                    (function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',w.intercomSettings);}else{var d=document;var i=function(){i.c(arguments);};i.q=[];i.c=function(args){i.q.push(args);};w.Intercom=i;var l=function(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/iq981kz6';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);};if(document.readyState==='complete'){l();}else if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();
                </script>
            @endif
        @endauth
    </body>
</html>
