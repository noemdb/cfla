<html class="dark scroll-smooth">

<head>
    <title>@yield('title')</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite('resources/css/app.css')
    @vite(['resources/js/app.js'])

    <wireui:scripts />
    @livewireStyles

</head>

<body class="flex flex-col min-h-screen bg-white dark:bg-black">

    <x-notifications />

    <div id="header"> @yield('header') </div>

    <div id="main" class="flex-1"> @yield('main') </div>

    <div id="footer"> @yield('footer') </div>

    @livewireScripts

</body>

</html>