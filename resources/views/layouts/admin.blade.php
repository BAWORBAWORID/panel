<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>{{ config('app.name', 'Pterodactyl') }} - @yield('title')</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <meta name="_token" content="{{ csrf_token() }}">
        <link rel="apple-touch-icon" sizes="180x180" href="/favicons/apple-touch-icon.png">
        <link rel="icon" type="image/png" href="/favicons/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="/favicons/favicon-16x16.png" sizes="16x16">
        <link rel="manifest" href="/favicons/manifest.json">
        <link rel="mask-icon" href="/favicons/safari-pinned-tab.svg" color="#bc6e3c">
        <link rel="shortcut icon" href="/favicons/favicon.ico">
        <meta name="msapplication-config" content="/favicons/browserconfig.xml">
        <meta name="theme-color" content="#0e4688">

    @include('layouts.scripts')

    @section('scripts')
        {!! Theme::css('vendor/select2/select2.min.css?t={cache-version}') !!}
        {!! Theme::css('vendor/bootstrap/bootstrap.min.css?t={cache-version}') !!}
        {!! Theme::css('vendor/adminlte/admin.min.css?t={cache-version}') !!}
        {!! Theme::css('vendor/adminlte/colors/skin-blue.min.css?t={cache-version}') !!}
        {!! Theme::css('vendor/sweetalert/sweetalert.min.css?t={cache-version}') !!}
        {!! Theme::css('vendor/animate/animate.min.css?t={cache-version}') !!}
        {!! Theme::css('css/pterodactyl.css?t={cache-version}') !!}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">

        @show
</head>
<body class="hold-transition skin-blue fixed sidebar-mini">
    <div class="wrapper">

        <header class="main-header">
            <a href="{{ route('index') }}" class="logo">
                <span>{{ config('app.name', 'Pterodactyl') }}</span>
            </a>
            <nav class="navbar navbar-static-top">
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <li class="user-menu">
                            <a href="{{ route('account') }}">
                                <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(Auth::user()->email)) }}?s=160" class="user-image" alt="User Image">
                                <span class="hidden-xs">{{ Auth::user()->name_first }} {{ Auth::user()->name_last }}</span>
                            </a>
                        </li>
                        <li>
                            <li><a href="{{ route('index') }}" data-toggle="tooltip" data-placement="bottom" title="Exit Admin Control"><i class="fa fa-server"></i></a></li>
                        </li>
                        <li>
                            <li><a href="{{ route('auth.logout') }}" id="logoutButton" data-toggle="tooltip" data-placement="bottom" title="Logout"><i class="fa fa-sign-out"></i></a></li>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <aside class="main-sidebar">
            <section class="sidebar">
                <ul class="sidebar-menu">
                    <li class="header">BASIC ADMINISTRATION</li>
                    <li class="{{ Route::currentRouteName() !== 'admin.index' ?: 'active' }}">
                        <a href="{{ route('admin.index') }}">
                            <i class="fa fa-home"></i> <span>Overview</span>
                        </a>
                    </li>
                    <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.settings') ?: 'active' }}">
                        <a href="{{ route('admin.settings')}}">
                            <i class="fa fa-wrench"></i> <span>Settings</span>
                        </a>
                    </li>
                    <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.api') ?: 'active' }}">
                        <a href="{{ route('admin.api.index')}}">
                            <i class="fa fa-gamepad"></i> <span>Application API</span>
                        </a>
                    </li>
<li class="{{ ! starts_with(Route::currentRouteName(), 'admin.alerts') ?: 'active' }}">
    <a href="{{ route('admin.alerts.index') }}">
        <i class="fa fa-bullhorn"></i> <span>Alerts</span>
    </a>
</li>
                    <li class="header">MANAGEMENT</li>
                    <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.databases') ?: 'active' }}">
                        <a href="{{ route('admin.databases') }}">
                            <i class="fa fa-database"></i> <span>Databases</span>
                        </a>
                    </li>
                    <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.locations') ?: 'active' }}">
                        <a href="{{ route('admin.locations') }}">
                            <i class="fa fa-globe"></i> <span>Locations</span>
                        </a>
                    </li>
                    <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.nodes') ?: 'active' }}">
                        <a href="{{ route('admin.nodes') }}">
                            <i class="fa fa-sitemap"></i> <span>Nodes</span>
                        </a>
                    </li>
                    <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.servers') ?: 'active' }}">
                        <a href="{{ route('admin.servers') }}">
                            <i class="fa fa-server"></i> <span>Servers</span>
                        </a>
                    </li>
                    <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.users') ?: 'active' }}">
                        <a href="{{ route('admin.users') }}">
                            <i class="fa fa-users"></i> <span>Users</span>
                        </a>
                    </li>
                    <li class="header">SERVICE MANAGEMENT</li>
                    <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.mounts') ?: 'active' }}">
                        <a href="{{ route('admin.mounts') }}">
                            <i class="fa fa-magic"></i> <span>Mounts</span>
                        </a>
                    </li>
                    <li class="{{ ! starts_with(Route::currentRouteName(), 'admin.nests') ?: 'active' }}">
                        <a href="{{ route('admin.nests') }}">
                            <i class="fa fa-th-large"></i> <span>Nests</span>
                        </a>
                    </li>
                </ul>
            </section>
        </aside>
        
        <div class="content-wrapper">
            

            {{-- ── GLOBAL ADMIN ALERT BANNER ──────────────────────── --}}
            @php $globalAlert = \Pterodactyl\Models\AdminAlert::getActive(); @endphp

            @if ($globalAlert)
                @php
                    $bg     = $globalAlert->bg_color     ?? '#1a1a2e';
                    $border = $globalAlert->border_color ?? '#4a5568';
                    $text   = $globalAlert->text_color   ?? '#e2e8f0';
                    $icon   = '/assets/alert-icons/' . ($globalAlert->icon ?? 'megaphone') . '.png';
                    $sticky = ($globalAlert->position ?? 'sticky') === 'sticky';
                    $name   = $globalAlert->creator
                        ? trim($globalAlert->creator->name_first . ' ' . $globalAlert->creator->name_last)
                        : 'Administrator';
                    $key    = 'admin_alert_v3_' . $globalAlert->id;
                @endphp

                <div id="g-alert" style="
                    width:100%; box-sizing:border-box;
                    background:{{ $bg }};
                    border-bottom:2px solid {{ $border }};
                    display:flex; align-items:flex-start; gap:12px;
                    padding:10px 20px;
                    {{ $sticky ? 'position:sticky;top:0;z-index:800;' : '' }}
                ">
                    <img src="{{ $icon }}" style="width:22px;height:22px;flex-shrink:0;object-fit:contain;margin-top:1px;filter:drop-shadow(0 0 4px {{ $border }}66);" alt="">

                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;flex-wrap:wrap;align-items:baseline;gap:6px;">
                            <span style="font-size:13px;font-weight:700;color:{{ $text }};letter-spacing:0.04em;text-transform:uppercase;white-space:nowrap;line-height:1.4;">
                                {{ $globalAlert->title }}
                            </span>
                            <span style="font-size:13px;color:{{ $text }};opacity:0.78;line-height:1.5;word-break:break-word;">
                                {{ $globalAlert->message }}
                            </span>
                        </div>
                        <div style="font-size:11px;color:{{ $text }};opacity:0.4;margin-top:4px;font-style:italic;">
                            By {{ $name }}
                        </div>
                    </div>

                    @if ($globalAlert->dismissable)
                        <button onclick="gAlertDismiss('{{ $key }}')"
                            style="background:transparent;border:1px solid {{ $border }}88;border-radius:4px;color:{{ $text }};font-size:12px;padding:4px 10px;cursor:pointer;white-space:nowrap;flex-shrink:0;opacity:0.72;font-family:inherit;transition:opacity .15s,border-color .15s;margin-top:1px;"
                            onmouseover="this.style.opacity='1';this.style.borderColor='{{ $border }}';"
                            onmouseout="this.style.opacity='0.72';this.style.borderColor='{{ $border }}88';">
                            ✕ Dismiss
                        </button>
                    @endif
                </div>

                @if ($globalAlert->dismissable)
                <script>
                    (function(){
                        if(localStorage.getItem('{{ $key }}')){
                            var el=document.getElementById('g-alert');
                            if(el) el.style.display='none';
                        }
                    })();
                    function gAlertDismiss(k){
                        localStorage.setItem(k,'1');
                        var el=document.getElementById('g-alert');
                        if(!el) return;
                        el.style.transition='opacity .3s,max-height .3s,padding .3s';
                        el.style.maxHeight=el.offsetHeight+'px';
                        el.style.overflow='hidden';
                        requestAnimationFrame(function(){
                            el.style.opacity='0';
                            el.style.maxHeight='0';
                            el.style.paddingTop='0';
                            el.style.paddingBottom='0';
                            el.style.borderWidth='0';
                        });
                        setTimeout(function(){ el.style.display='none'; }, 320);
                    }
                </script>
                @endif
            @endif
            {{-- ── END BANNER ──────────────────────────────────────── --}}



            <section class="content-header">
                @yield('content-header')
            </section>
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                There was an error validating the data provided.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @foreach (Alert::getMessages() as $type => $messages)
                            @foreach ($messages as $message)
                                <div class="alert alert-{{ $type }} alert-dismissable" role="alert">
                                    {{ $message }}
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
                @yield('content')
            </section>
        </div>
        <footer class="main-footer">
            <div class="pull-right small text-gray" style="margin-right:10px;margin-top:-7px;">
                <strong><i class="fa fa-fw {{ $appIsGit ? 'fa-git-square' : 'fa-code-fork' }}"></i></strong> {{ $appVersion }}<br />
                <strong><i class="fa fa-fw fa-clock-o"></i></strong> {{ round(microtime(true) - LARAVEL_START, 3) }}s
            </div>
            Copyright &copy; 2015 - {{ date('Y') }}
            <a href="https://pterodactyl.io/">Pterodactyl Software</a>
            &bull; Co-Engineered with
            <a href="{{ $_abe ?? '' }}" target="_blank">
                {{ $_abl ?? '' }}
            </a>.
        </footer>
    </div>
    @section('footer-scripts')
        <script src="/js/keyboard.polyfill.js" type="application/javascript"></script>
        <script>keyboardeventKeyPolyfill.polyfill();</script>

        {!! Theme::js('vendor/jquery/jquery.min.js?t={cache-version}') !!}
        {!! Theme::js('vendor/sweetalert/sweetalert.min.js?t={cache-version}') !!}
        {!! Theme::js('vendor/bootstrap/bootstrap.min.js?t={cache-version}') !!}
        {!! Theme::js('vendor/slimscroll/jquery.slimscroll.min.js?t={cache-version}') !!}
        {!! Theme::js('vendor/adminlte/app.min.js?t={cache-version}') !!}
        {!! Theme::js('vendor/bootstrap-notify/bootstrap-notify.min.js?t={cache-version}') !!}
        {!! Theme::js('vendor/select2/select2.full.min.js?t={cache-version}') !!}
        {!! Theme::js('js/admin/functions.js?t={cache-version}') !!}
        <script src="/js/autocomplete.js" type="application/javascript"></script>

        @if(Auth::user()->root_admin)
            <script>
                $('#logoutButton').on('click', function (event) {
                    event.preventDefault();

                    var that = this;
                    swal({
                        title: 'Do you want to log out?',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d9534f',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Log out'
                    }, function () {
                         $.ajax({
                            type: 'POST',
                            url: '{{ route('auth.logout') }}',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },complete: function () {
                                window.location.href = '{{route('auth.login')}}';
                            }
                    });
                });
            });
            </script>
        @endif

        <script>
            $(function () {
                $('[data-toggle="tooltip"]').tooltip();
            })
        </script>
    @show
</body>
</html>
