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
    <link id="theme-css" href="{{ asset('css/theme.'.((auth()->user()) ? auth()->user()->theme : 'light').'.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <b-navbar toggleable="lg" variant="secondary">
            <b-navbar-brand href="{{ url('/') }}">
                {{--  <img height="75" src="{{ url('/logo.jpg') }}" alt="{{ config('app.name', 'Laravel') }}" />  --}}
            </b-navbar-brand>
            <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>
            <b-collapse id="nav-collapse" is-nav>
                <b-navbar-nav>
                    <b-nav-item href="/">Home</b-nav-item>
                    @auth
                    <!-- revert 'u' permission in productive mode -->
                    @if(in_array(auth()->user()->role, ['a', 's'])) 
                    <b-nav-item href="{{ route('upload.game.view') }}"><b-icon-upload></b-icon-upload>&nbsp;Upload Game</b-nav-item>@endif
                    @if(in_array(auth()->user()->role, ['s']))
                    <b-nav-item-dropdown>
                        <template #button-content>
                            <b-icon-award></b-icon-award>
                            <strong>Awards</strong>
                        </template>
                        <b-dropdown-item href="{{ route('upload.award.view') }}">Upload</b-dropdown-item>
                        <b-dropdown-item href="{{ route('assign.award.view') }}">Assign / Edit</b-dropdown-item>
                    </b-nav-item-dropdown>
                    @endif
                    @endauth
                    <b-nav-item href="{{ route('results') }}">Results</b-nav-item>
                    <b-nav-item href="{{ route('results.ranking') }}">Ranking</b-nav-item>
                    <b-nav-item href="{{ route('player.all') }}">Players</b-nav-item>
                    {{--  <b-nav-item href="{{ route('results.halloffame') }}">Hall of Fame</b-nav-item>  --}}
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
    </div>
    <script>
        window.arole = "{!! (auth()->user()) ? auth()->user()->role : '' !!}";
        document.addEventListener('DOMContentLoaded', function(event) {
            $('#theme-toggle').click(function(e){
                e.preventDefault();
                let href = $('#theme-css').attr('href');
                let theme = 'dark';
                if(href.indexOf('dark') !== -1){
                    href = href.replace('dark', 'light');
                    theme = 'light'
                }else if(href.indexOf('light') !== -1){
                    href = href.replace('light', 'dark');
                }
                // save theme into session
                axios.get('{{ route('user.theme.set') }}' + '?v=' + theme);

                $('#theme-css').attr('href', href);
            });
        });
    </script>
</body>
</html>
