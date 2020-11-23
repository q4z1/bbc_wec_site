<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link id="themecss" href="{{ asset('css/theme.dark.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <b-navbar toggleable="lg" variant="secondary">
            <b-navbar-brand href="{{ url('/') }}"><img src="{{ url('/logo.jpg') }}" alt="{{ config('app.name', 'Laravel') }}" /></b-navbar-brand>
            <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>
            <b-collapse id="nav-collapse" is-nav>
                <b-navbar-nav>
                    @guest
                    <b-nav-item href="#">Guest Link</b-nav-item>
                    @else
                    <b-nav-item href="{{ route('upload.game.view') }}">Upload Game</b-nav-item>
                    @endguest
                </b-navbar-nav>
                <b-navbar-nav class="ml-auto">
                    Theme
                </b-navbar-nav>
                <!-- Right aligned nav items -->
                <b-navbar-nav>
                    <b-nav-item-dropdown right>
                        <!-- Using 'button-content' slot -->
                        <template #button-content>
                            @guest
                            <strong>User</strong>
                            @else
                            <strong>{{ Auth::user()->name }}</strong>
                            @endguest
                        </template>
                        @guest
                            @if (Route::has('login'))
                                <b-dropdown-item href="{{ route('login') }}">{{ __('Login') }}</b-dropdown-item>
                            @endif
                            @if (Route::has('register'))
                                <b-dropdown-item href="{{ route('register') }}">{{ __('Register') }}</b-dropdown-item>
                            @endif
                        @else
                            <b-dropdown-item>
                                <a onclick="window.event.preventDefault();document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
                            </b-dropdown-item>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        @endguest
                    </b-nav-item-dropdown>
                </b-navbar-nav>
            </b-collapse>
        </b-navbar>
        <main class="py-4" style="padding: 15px">
            @yield('content')
        </main>
    </div>
</body>
</html>
