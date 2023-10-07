<!DOCTYPE html>
<html>
<head>
    <title>Custom Auth in Laravel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div id="app">
    <script>
        window.Echo.connector.pusher.connection.bind('connected', () => {
            console.log('connected');
        });

        window.Echo.channel('public')
            .listen('PublicEvent', (e) => {
                console.log(e);
            });

        @if (auth())
            window.Echo.private('alert.{{auth()->id()}}')
                .listen('AlertEvent', (e) => {
                        console.log(e);
                    }
                );

            window.Echo.private('metrics.{{auth()->id()}}')
                .listen('MetricsEvent', (e) => {
                        console.log(e);
                    }
                );
        @endif
    </script>
    <nav class="navbar navbar-light navbar-expand-lg mb-5" style="background-color: #e3f2fd;">
        <div class="container">
            <a class="navbar-brand mr-auto" href="#">ConnectLoc</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('registration') }}">Register</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('logout') }}">Logout</a>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        @if(session('success-message'))
            <h1>{{session('success-message')}}</h1>
        @endif
            @if(session('error-message'))
                <h1>{{session('error-message')}}</h1>
            @endif
        @if(session('success'))
        <h1>{{session('success')}}</h1>
    @endif
    </div>
    @yield('content')
    <script type="text/javascript" src="/js/app.js"></script>
</div>
</body>
</html>
