<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php($theme = request()->cookie('theme', (auth()->user() ? auth()->user()->theme : 'light')))
    @php($pageTitle = trim($__env->yieldContent('title') ?: config('app.name', 'WeCup')))
    @php($pageDescription = $__env->yieldContent('description')
        ?: 'Results, rankings and hall of fame of the WeCup, the PokerTH tournament series.')

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:site_name" content="{{ config('app.name', 'WeCup') }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ url('/logo.png') }}">
    <meta name="twitter:card" content="summary">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('favicon-48.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon-180.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#1a1a1a">

    {{-- Die Webfonts liegen unter /public/fonts, es geht also kein Request an
         fonts.gstatic.com hinaus. Das Preload sorgt dafuer, dass schon der
         erste Paint die richtige Schrift benutzt statt spaeter umzuspringen. --}}
    <link rel="preload" as="font" type="font/woff2" crossorigin href="{{ asset('fonts/nunito-latin.woff2') }}">
    @if($theme === 'dark')
        <link rel="preload" as="font" type="font/woff2" crossorigin href="{{ asset('fonts/sourcesans-normal-400-latin.woff2') }}">
    @else
        <link rel="preload" as="font" type="font/woff2" crossorigin href="{{ asset('fonts/opensans-normal-400-700-latin.woff2') }}">
    @endif

    @vite(['resources/js/app.js', 'resources/sass/app.scss'])
    <link id="theme-css" rel="stylesheet" href="{{ asset('css/theme.' . $theme . '.css') }}">
</head>
<body data-theme="{{ $theme }}">
    <div id="app">
        <nav class="main-navbar">
            <!-- Brand + Hamburger -->
            <div class="navbar-brand">
                <a href="{{ url('/') }}">
                    <img src="{{ url('/logo.png') }}" width="128" height="75" alt="{{ config('app.name', 'WeCup') }}" />
                </a>
                <button type="button" class="main-navbar-toggler" @click="mobileMenuOpen = !mobileMenuOpen" aria-label="Menu">
                    <span></span><span></span><span></span>
                </button>
            </div>

            <!-- Rechte Seite: immer sichtbar -->
            <div class="main-navbar-end" v-cloak>
                <el-tooltip content="Theme switch" placement="bottom-end">
                    <el-button class="theme-toggle-btn" :icon="Sunny" circle></el-button>
                </el-tooltip>
                <el-dropdown trigger="click" placement="bottom-end">
                    <button type="button" class="navbar-user-trigger" title="Profile">
                        <el-icon><avatar></avatar></el-icon>
                        @auth&nbsp;<strong>{{ Auth::user()->name }}</strong>@endauth
                    </button>
                    <template v-slot:dropdown>
                        <el-dropdown-menu>
                            @guest
                                @if (Route::has('login'))
                                <el-dropdown-item onclick="window.location.href='{{ route('login') }}'">{{ __('Login') }}</el-dropdown-item>
                                @endif
                                @if (Route::has('register'))
                                <el-dropdown-item onclick="window.location.href='{{ route('register') }}'">{{ __('Register') }}</el-dropdown-item>
                                @endif
                            @else
                                <el-dropdown-item onclick="document.getElementById('logout-form').submit()">{{ __('Logout') }}</el-dropdown-item>
                            @endguest
                        </el-dropdown-menu>
                    </template>
                </el-dropdown>
            </div>

            <!-- Kollabierbare Nav-Items -->
            <div :class="['main-navbar-collapse', { 'is-open': mobileMenuOpen }]" v-cloak>
                <el-menu mode="horizontal" :ellipsis="!mobileMenuOpen" style="width:100%;" class="main-navbar-items">
                    @auth
                    @if(in_array(auth()->user()->role, ['a', 's']))
                    <el-sub-menu index="admin">
                        <template v-slot:title><el-icon><tools></tools></el-icon>&nbsp;<strong>Admin</strong></template>
                        <el-menu-item index="admin-upload"><a href="{{ route('upload.game.view') }}"><el-icon><upload></upload></el-icon>&nbsp;Upload Game</a></el-menu-item>
                        @if(auth()->user()->role === 's')
                        <el-menu-item index="admin-awards"><a href="{{ route('award.view') }}"><el-icon><medal></medal></el-icon>&nbsp;Awards</a></el-menu-item>
                        @endif
                    </el-sub-menu>
                    @endif
                    @endauth

                    <el-menu-item index="home"><a href="{{ url('/') }}"><el-icon><house></house></el-icon>&nbsp;Home</a></el-menu-item>
                    <el-menu-item index="results"><a href="{{ route('results') }}"><el-icon><notebook></notebook></el-icon>&nbsp;Results</a></el-menu-item>
                    <el-menu-item index="ranking"><a href="{{ route('results.ranking') }}"><el-icon><trophy></trophy></el-icon>&nbsp;Ranking</a></el-menu-item>
                    <el-menu-item index="players"><a href="{{ route('players') }}"><el-icon><user-filled></user-filled></el-icon>&nbsp;Players</a></el-menu-item>
                    {{-- <el-menu-item index="halloffame"><a href="{{ route('results.halloffame') }}"><el-icon><star></star></el-icon>&nbsp;Hall of Fame</a></el-menu-item> --}}
                </el-menu>
            </div>
        </nav>

        @auth
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
        @endauth

        <main style="padding: 1.5rem 0;">
            <div style="width:100%; padding: 0 1rem;">
                @yield('content')
            </div>
        </main>

        <footer class="page-footer">
            <a href="https://pokerth.net/app.php/imprint" title="Imprint">Imprint</a>
        </footer>
    </div>

    <script>
        window.arole = "{!! (auth()->user()) ? auth()->user()->role : '' !!}";
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.theme-toggle-btn').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const link = document.getElementById('theme-css');
                    const href = link.getAttribute('href');
                    const theme = href.indexOf('dark') !== -1 ? 'light' : 'dark';
                    link.setAttribute('href', '/css/theme.' + theme + '.css');
                    document.body.setAttribute('data-theme', theme);
                    // Cookie fuer ein Jahr - greift auch fuer Gaeste
                    document.cookie = 'theme=' + theme + '; path=/; max-age=31536000; SameSite=Lax';
                    // Eingeloggte User bekommen es zusaetzlich ins Profil
                    window.axios && window.axios.get('{{ route('user.theme.set') }}?v=' + theme);
                });
            });
        });
    </script>
</body>
</html>
