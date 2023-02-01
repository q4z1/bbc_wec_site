<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3e%3ctext y='.9em' font-size='90'%3e🏆%3c/text%3e%3c/svg%3e">

    <!-- Scripts -->
    <script src="{{ asset(mix('js/app.js')) }}" defer></script>

    <!-- Fonts -->
    <!-- <link rel="dns-prefetch" href="//fonts.gstatic.com"> -->
    <!-- <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet"> -->

    <!-- Styles -->
    <link href="{{ asset(mix('css/app.css')) }}" rel="stylesheet">
    <link id="theme-css" href="{{ asset(mix('css/theme.'.((auth()->user()) ? auth()->user()->theme : 'light').'.css')) }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <b-navbar toggleable="lg" variant="secondary">
            <b-navbar-brand href="{{ url('/') }}"><img height="75" src="{{ url('/logo.jpg') }}" alt="{{ config('app.name', 'Laravel') }}" /></b-navbar-brand>
            <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>
            <b-collapse id="nav-collapse" is-nav>
                <b-navbar-nav>
                    <b-nav-item href="/">Home</b-nav-item>
                    @auth
                    @if(in_array(auth()->user()->role, ['a', 's']))
                    <b-nav-item href="{{ route('upload.game.view') }}"><b-icon-upload></b-icon-upload>&nbsp;Upload Game</b-nav-item>
                    @endif
                    @if(in_array(auth()->user()->role, ['s']))
                    <b-nav-item href="{{ route('award.view') }}"><b-icon-award></b-icon-award>&nbsp;Awards</b-nav-item>
                    @endif
                    @endauth
                    <b-nav-item href="{{ route('results') }}"><b-icon-book-fill></b-icon-book-fill>&nbsp;Results</b-nav-item>
                    <b-nav-item href="{{ route('results.ranking') }}"><b-icon-trophy-fill></b-icon-trophy-fill>&nbsp;Ranking</b-nav-item>
                    <b-nav-item href="{{ route('players') }}"><b-icon-person-fill></b-icon-person-fill>&nbsp;Players</b-nav-item>
                    {{-- <b-nav-item href="{{ route('results.halloffame') }}">Hall of Fame</b-nav-item> --}}
                </b-navbar-nav>
                <!-- Right aligned nav items -->
                <b-navbar-nav class="ml-auto">
                    @auth
                    <b-nav-item id="theme-toggle" v-b-tooltip.hover title="Toggle Theme"><b-icon-front></b-icon-front></b-nav-item>
                    @endauth
                    {{--  <b-nav-item href="{{ route('shoutbox') }}" v-b-tooltip.hover title="Shoutbox"><b-icon-chat-text></b-icon-chat-text></b-nav-item>  --}}
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
