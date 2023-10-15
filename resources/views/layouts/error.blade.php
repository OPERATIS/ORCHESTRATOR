<!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="alternate icon" type="image/png" href="{{asset('/favicon.png')}}">
        <link rel="icon" type="image/svg+xml" href="{{asset('/favicon.svg')}}">

        {!! SEO::generate() !!}
        @if (!SEO::getTitle())
            <title>
                {{ ucfirst(collect(explode('/',url()->current()))->last()) }} - ORCHESTRATOR
            </title>
        @endif

        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        <script src="{{ mix('js/app.js') }}" defer></script>
    </head>
    <body class="font-inter antialiased !bg-body text-black">
        <div id="app">
            @yield('content')
        </div>
    </body>

</html>
