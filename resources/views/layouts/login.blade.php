<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet">

{{--        <link rel="apple-touch-icon" sizes="57x57" href="{{asset('/favicon/apple-icon-57x57.png')}}">--}}
{{--        <link rel="manifest" href="{{asset('/favicon/manifest.json')}}">--}}

        <title>ORCHESTRATOR</title>
        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        <!-- Scripts -->
        <script src="{{ mix('js/app.js') }}" defer></script>
    </head>
    <body class="font-inter antialiased !bg-body text-black">
        <div id="app">
            @yield('content')
        </div>
    </body>

</html>
