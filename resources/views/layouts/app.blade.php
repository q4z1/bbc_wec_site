<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BBC') }}</title>

    <!-- Scripts -->
    <script src="{{ asset(mix('js/app.js')) }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset(mix('css/app.css')) }}" rel="stylesheet">
    <link id="theme-css" href="{{ asset(mix('css/theme.'.((auth()->user()) ? auth()->user()->theme : 'light').'.css')) }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <b-navbar toggleable="lg" variant="secondary">
            <b-navbar-brand href="{{ url('/') }}">
                <img width="150" src="{{ url('/logo.png') }}" alt="{{ config('app.name', 'Best Brainies Cup') }}" />
            </b-navbar-brand>
            <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>
            <b-collapse id="nav-collapse" is-nav>
                <b-navbar-nav>
                    <!-- <b-nav-item href="/"><b-icon-house-fill></b-icon-house-fill>&nbsp;Home</b-nav-item> -->
                    @auth
                    <!-- revert 'u' permission in productive mode -->
                    @if(auth()->user()->role === 's') 
                    <b-nav-item-dropdown>
                        <!-- Using 'button-content' slot -->
                        <template #button-content>
                            <b-icon-tools></b-icon-tools>
                            <strong>Admin</strong>
                        </template>
                        <b-dropdown-item href="{{ route('upload.game.view') }}"><b-icon-upload></b-icon-upload>&nbsp;Upload Game</b-dropdown-item>
                        <b-dropdown-item  href="{{ route('award.view') }}"><b-icon-award></b-icon-award>&nbsp;Awards</b-dropdown-item>
                        <b-dropdown-item  href="{{ route('user.view') }}"><b-icon-person-fill></b-icon-person-fill>&nbsp;Users</b-dropdown-item>
                        <b-dropdown-item  href="{{ route('pages') }}"><b-icon-file-text></b-icon-file-text>&nbsp;Pages</b-dropdown-item>
                        <b-dropdown-item  href="{{ route('botfiles.show') }}"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-robot" viewBox="0 0 16 16">
                          <path d="M6 12.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5M3 8.062C3 6.76 4.235 5.765 5.53 5.886a26.6 26.6 0 0 0 4.94 0C11.765 5.765 13 6.76 13 8.062v1.157a.93.93 0 0 1-.765.935c-.845.147-2.34.346-4.235.346s-3.39-.2-4.235-.346A.93.93 0 0 1 3 9.219zm4.542-.827a.25.25 0 0 0-.217.068l-.92.9a25 25 0 0 1-1.871-.183.25.25 0 0 0-.068.495c.55.076 1.232.149 2.02.193a.25.25 0 0 0 .189-.071l.754-.736.847 1.71a.25.25 0 0 0 .404.062l.932-.97a25 25 0 0 0 1.922-.188.25.25 0 0 0-.068-.495c-.538.074-1.207.145-1.98.189a.25.25 0 0 0-.166.076l-.754.785-.842-1.7a.25.25 0 0 0-.182-.135"/>
                          <path d="M8.5 1.866a1 1 0 1 0-1 0V3h-2A4.5 4.5 0 0 0 1 7.5V8a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1v-.5A4.5 4.5 0 0 0 10.5 3h-2zM14 7.5V13a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V7.5A3.5 3.5 0 0 1 5.5 4h5A3.5 3.5 0 0 1 14 7.5"/>
                        </svg>&nbsp;Botfiles</b-dropdown-item>
                    </b-nav-item-dropdown>
                    @elseif(auth()->user()->role === 'a')
                    <b-nav-item-dropdown>
                        <!-- Using 'button-content' slot -->
                        <template #button-content>
                            <b-icon-tools></b-icon-tools>
                            <strong>Admin</strong>
                        </template>
                    <b-dropdown-item href="{{ route('upload.game.view') }}"><b-icon-upload></b-icon-upload>&nbsp;Upload Game</b-dropdown-item>
                    <b-dropdown-item  href="{{ route('award.view') }}"><b-icon-award></b-icon-award>&nbsp;Awards</b-dropdown-item>
                    <b-dropdown-item  href="{{ route('pages') }}"><b-icon-file-text></b-icon-file-text>&nbsp;Pages</b-dropdown-item>
                    </b-nav-item-dropdown>
                    @endif
                    @endauth
                    <b-nav-item href="{{ route('results') }}"><b-icon-book-fill></b-icon-book-fill>&nbsp;Results</b-nav-item>
                    <b-nav-item href="{{ route('results.ranking') }}"><b-icon-trophy-fill></b-icon-trophy-fill>&nbsp;Ranking</b-nav-item>
                    <b-nav-item href="{{ route('player.all') }}"><b-icon-person-fill></b-icon-person-fill>&nbsp;Players</b-nav-item>
                    <b-nav-item href="{{ route('registration') }}"><b-icon-calendar2-check-fill></b-icon-calendar2-check-fill>&nbsp;Registration</b-nav-item>
                    @foreach( \App\Models\Page::where([['active', 1], ['slug', '!=', 'home']])->orderBy('order', 'ASC')->get() as $page)
                    <b-nav-item href="/page/{{ $page->slug }}">{{ $page->title }}</b-nav-item>
                    @endforeach
                </b-navbar-nav>
                <!-- Right aligned nav items -->
                <b-navbar-nav class="ml-auto">
                    @auth
                    <b-nav-item id="theme-toggle" v-b-tooltip.hover title="Toggle Theme"><b-icon-front></b-icon-front></b-nav-item>
                    @endauth
                    <b-nav-item href="{{ route('shoutbox') }}" v-b-tooltip.hover title="Shoutbox"><b-icon-chat-text></b-icon-chat-text></b-nav-item>
                    <b-nav-item-dropdown right>
                        <!-- Using 'button-content' slot -->
                        <template #button-content>
                            <b-icon-person-circle></b-icon-person-circle>
                            @auth
                            <strong>{{ Auth::user()->name }}</strong>
                            @endauth
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
        <main class="py-4">
            <b-container fluid>
                @yield('content')
            </b-container>
        </main>
        <footer>
          <b-row>
            <b-col><a href="https://pokerth.net/app.php/imprint" title="Imprint">Imprint</a></b-col>
          </b-row>
        </footer>
    </div>
    <script>
        window.arole = "{!! (auth()->user()) ? auth()->user()->role : '' !!}";
        document.addEventListener('DOMContentLoaded', function(event) {
            $('#theme-toggle').click(function(e){
                e.preventDefault();
                let href = $('#theme-css').attr('href');
                let theme = 'dark';
                if(href.indexOf('dark') !== -1){
                    href = "{{ asset(mix('css/theme.light.css')) }}";
                    theme = 'light'
                }else if(href.indexOf('light') !== -1){
                    href = "{{ asset(mix('css/theme.dark.css')) }}";
                }
                // save theme into session
                axios.get('{{ route('user.theme.set') }}' + '?v=' + theme);
                $('#theme-css').attr('href', href);
            });
        });
    </script>
</body>
</html>
