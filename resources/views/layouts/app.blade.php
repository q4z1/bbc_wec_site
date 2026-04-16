<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'BBC') }}</title>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    @vite(['resources/js/app.js', 'resources/sass/app.scss'])
    <link id="theme-css" rel="stylesheet" href="{{ asset('css/theme.' . (request()->cookie('theme', (auth()->user() ? auth()->user()->theme : 'light'))) . '.css') }}">
</head>
<body data-theme="{{ request()->cookie('theme', (auth()->user() ? auth()->user()->theme : 'light')) }}">
    <div id="app">
        <nav class="main-navbar">
            <!-- Brand + Hamburger (direkt rechts neben Logo) -->
            <div class="navbar-brand">
                <a href="{{ url('/') }}">
                    <img width="150" src="{{ url('/logo.png') }}" alt="{{ config('app.name', 'Best Brainies Cup') }}" />
                </a>
                <button type="button" class="main-navbar-toggler" @click="mobileMenuOpen = !mobileMenuOpen" aria-label="Menü">
                    <span></span><span></span><span></span>
                </button>
            </div>

            <!-- Rechte Seite: immer sichtbar -->
            <div class="main-navbar-end">
                <el-tooltip content="Theme wechseln" placement="bottom">
                    <el-button class="theme-toggle-btn" :icon="Sunny" circle />
                </el-tooltip>
                <a href="{{ route('shoutbox') }}" class="navbar-icon-link">
                    <el-icon style="font-size:1.4rem;"><chat-dot-round /></el-icon>
                </a>
                <el-dropdown trigger="click" placement="bottom-end">
                    <button type="button" class="navbar-user-trigger">
                        <el-icon><Avatar /></el-icon>
                        @auth&nbsp;<strong>{{ Auth::user()->name }}</strong>@endauth
                    </button>
                    <template #dropdown>
                        <el-dropdown-menu>
                            @guest
                                @if(Route::has('login'))
                                <el-dropdown-item onclick="window.location.href='{{ route('login') }}'">{{ __('Login') }}</el-dropdown-item>
                                @endif
                                @if(Route::has('register'))
                                <el-dropdown-item onclick="window.location.href='{{ route('register') }}'">{{ __('Register') }}</el-dropdown-item>
                                @endif
                            @else
                                <el-dropdown-item onclick="document.getElementById('logout-form').submit()">{{ __('Logout') }}</el-dropdown-item>
                            @endguest
                        </el-dropdown-menu>
                    </template>
                </el-dropdown>
            </div>

            <!-- Kollabierbare Nav-Items (per CSS order:2 auf Desktop zwischen Brand und End) -->
            <div :class="['main-navbar-collapse', { 'is-open': mobileMenuOpen }]">
                <el-menu mode="horizontal" :ellipsis="!mobileMenuOpen" style="width:100%;" class="main-navbar-items">
                    @auth
                    @if(auth()->user()->role === 's' || auth()->user()->role === 'a')
                    <el-sub-menu index="admin">
                        <template #title><el-icon><Tools /></el-icon>&nbsp;<strong>Admin</strong></template>
                        <el-menu-item index="admin-upload"><a href="{{ route('upload.game.view') }}"><el-icon><Upload /></el-icon>&nbsp;Upload Game</a></el-menu-item>
                        <el-menu-item index="admin-awards"><a href="{{ route('award.view') }}"><el-icon><Medal /></el-icon>&nbsp;Awards</a></el-menu-item>
                        <el-menu-item index="admin-users"><a href="{{ route('user.view') }}"><el-icon><User /></el-icon>&nbsp;Users</a></el-menu-item>
                        <el-menu-item index="admin-pages"><a href="{{ route('pages') }}"><el-icon><Document /></el-icon>&nbsp;Pages</a></el-menu-item>
                        <el-menu-item index="admin-botfiles"><a href="{{ route('botfiles.show') }}"><el-icon><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16"><path d="M6 12.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5M3 8.062C3 6.76 4.235 5.765 5.53 5.886a26.6 26.6 0 0 0 4.94 0C11.765 5.765 13 6.76 13 8.062v1.157a.93.93 0 0 1-.765.935c-.845.147-2.34.346-4.235.346s-3.39-.2-4.235-.346A.93.93 0 0 1 3 9.219zm4.542-.827a.25.25 0 0 0-.217.068l-.92.9a25 25 0 0 1-1.871-.183.25.25 0 0 0-.068.495c.55.076 1.232.149 2.02.193a.25.25 0 0 0 .189-.071l.754-.736.847 1.71a.25.25 0 0 0 .404.062l.932-.97a25 25 0 0 0 1.922-.188.25.25 0 0 0-.068-.495c-.538.074-1.207.145-1.98.189a.25.25 0 0 0-.166.076l-.754.785-.842-1.7a.25.25 0 0 0-.182-.135"/><path d="M8.5 1.866a1 1 0 1 0-1 0V3h-2A4.5 4.5 0 0 0 1 7.5V8a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1v-.5A4.5 4.5 0 0 0 10.5 3h-2zM14 7.5V13a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V7.5A3.5 3.5 0 0 1 5.5 4h5A3.5 3.5 0 0 1 14 7.5"/></svg></el-icon>&nbsp;Botfiles</a></el-menu-item>
                        <el-menu-item index="admin-fp"><a href="{{ route('fpnicksearch') }}"><el-icon><Search /></el-icon>&nbsp;Fingerprint Search</a></el-menu-item>
                        <el-menu-item index="admin-sbdel"><a href="{{ route('sbdel.view') }}"><el-icon><chat-line-round /></el-icon>&nbsp;Deleted Shoutbox Messages</a></el-menu-item>
                        <el-menu-item index="admin-actions"><a href="{{ route('actions.view') }}"><el-icon><Memo /></el-icon>&nbsp;Action-Log</a></el-menu-item>
                    </el-sub-menu>
                    @endif
                    @endauth

                    <el-menu-item index="results"><a href="{{ route('results') }}"><el-icon><Notebook /></el-icon>&nbsp;Results</a></el-menu-item>
                    <el-menu-item index="ranking"><a href="{{ route('results.ranking') }}"><el-icon><Trophy /></el-icon>&nbsp;Ranking</a></el-menu-item>
                    <el-menu-item index="players"><a href="{{ route('player.all') }}"><el-icon><user-filled /></el-icon>&nbsp;Players</a></el-menu-item>
                    <el-menu-item index="registration"><a href="{{ route('registration') }}"><el-icon><Calendar /></el-icon>&nbsp;Registration</a></el-menu-item>
                    @foreach(\App\Models\Page::where([['active', 1], ['slug', '!=', 'home']])->orderBy('order', 'ASC')->get() as $page)
                    <el-menu-item index="page-{{ $page->slug }}"><a href="/page/{{ $page->slug }}">{{ $page->title }}</a></el-menu-item>
                    @endforeach
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
            document.querySelectorAll('.theme-toggle-btn').forEach(function(btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const link = document.getElementById('theme-css');
                    const href = link.getAttribute('href');
                    const theme = href.indexOf('dark') !== -1 ? 'light' : 'dark';
                    link.setAttribute('href', '/css/theme.' + theme + '.css');
                    document.body.setAttribute('data-theme', theme);
                    // Cookie für 1 Jahr setzen (funktioniert für Gäste und eingeloggte User)
                    document.cookie = 'theme=' + theme + '; path=/; max-age=31536000; SameSite=Lax';
                    // Auch für eingeloggte User im Profil speichern
                    window.axios && window.axios.get('{{ route('user.theme.set') }}?v=' + theme);
                });
            });
        });
    </script>
</body>
</html>
