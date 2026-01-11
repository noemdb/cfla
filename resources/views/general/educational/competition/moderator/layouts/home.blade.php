<html class="dark scroll-smooth">

<head>

    <title>@yield('title')</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite('resources/css/app.css')
    @vite(['resources/js/app.js'])

    <wireui:scripts />
    @livewireStyles
    @include('general.educational.competition.partials.styles')
</head>

<body
    class="font-sans antialiased bg-gradient-to-br from-gray-900 via-emerald-900 to-gray-900 min-h-screen text-gray-100 flex flex-col">

    <x-notifications />

    <div id="header" data-header="header"> @yield('header') </div>

    <div id="main" class="flex-1"> @yield('main') </div>

    <div id="footer"> @yield('footer') </div>

    <!-- customScripts -->
    @yield('javascripts')

    @livewireScripts

</body>

</html>
