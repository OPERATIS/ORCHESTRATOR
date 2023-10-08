<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
{{--    <link rel="apple-touch-icon" sizes="128x128" href="/favicon.png">--}}
{{--    <link rel="icon" type="image/png" sizes="128x128" href="/favicon.png">--}}

    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">

    <title>ORCHESTRATOR</title>

    @stack('custom-styles')
</head>
<body class="min-h-screen antialiased bg-white">
    <div id="app">
        {{-- LEFT SIDEBAR--}}
        <div class="sidebar fixed absolute z-20 w-[13.25rem] z-20 ">
            @include('layouts._menu')
        </div>

        <div class="content lg:pl-[13.25rem]">
            @yield('content')
        </div>
    </div>
    <script src="{{ mix('/js/app.js') }}"></script>
</body>
</html>
