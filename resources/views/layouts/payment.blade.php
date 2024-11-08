<!-- resources/views/layouts/app.blade.php -->

<html class="scroll-smooth">

<head>

    <title>@yield('title')</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite('resources/css/app.css')
    @vite(['resources/js/app.js'])

    <wireui:scripts />
    @livewireStyles

</head>

<body class="dark:bg-black w-full sm:w-3/4 md:w-1/2 lg:w-1/3 xl:w-1/4 aspect-[9/16] bg-gray-200 mx-auto">          

    <x-notifications />

    <div id="header"> @yield('header') </div>

    <div class="text-xl text-green-950 font-bold text-center py-2 my-2 bg-white rounded">C.E. Colegio Fray Luis Amigó</div>

    <div id="main" class="py-1"> @yield('main') </div>

    <div id="footer"> @yield('footer') </div>

    @yield('customScripts')

    @livewireScripts


    @yield('scriptsLivewire')

</body>

</html>