<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
{{--    <link rel="apple-touch-icon" sizes="128x128" href="/favicon.png">--}}
{{--    <link rel="icon" type="image/png" sizes="128x128" href="/favicon.png">--}}

    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
    <script src="{{ mix('/js/app.js') }}"></script>

    <title>ORCHESTRATOR</title>

    @stack('custom-styles')
</head>
<body class="min-h-screen antialiased bg-body">

{{-- LEFT SIDEBAR--}}
<div class="sidebar fixed lg:absolute z-20 w-[20.25rem] md-max:w-full z-20 pl-12 pr-6 pt-12 pb-12 md-max:pt-0 md-max:p-0 lg:min-h-screen">
    @include('layouts._menu')
</div>

<div class="content lg:pl-[20.25rem] pt-12 pb-12 md-max:pt-14">
    @yield('content')
</div>

@livewireScripts
</body>
</html>
