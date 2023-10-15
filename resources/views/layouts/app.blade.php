<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="alternate icon" type="image/png" href="{{asset('/favicon.png')}}">
    <link rel="icon" type="image/svg+xml" href="{{asset('/favicon.svg')}}">

{{--    <link rel="icon" type="image/png" sizes="128x128" href="/favicon.png">--}}

    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">

    {!! SEO::generate() !!}
    @if (!SEO::getTitle())
        <title>
            {{ ucfirst(collect(explode('/',url()->current()))->last()) }} - ORCHESTRATOR
        </title>
    @endif

    @stack('custom-styles')
</head>
<body class="min-h-screen antialiased bg-white">
    <div id="app">
        {{-- LEFT SIDEBAR--}}
        <div class="sidebar fixed z-20 w-[13.25rem] z-20 ">
            @include('layouts._menu')
        </div>

        <div class="content lg:pl-[13.25rem]">
            @yield('content')
        </div>
    </div>
    @include('components.flash_messages')
    <script src="{{ mix('/js/app.js') }}"></script>
</body>
</html>
