<html class="scroll-smooth">

<head>

    <title>@yield('title')</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite('resources/css/app.css')
    @vite(['resources/js/app.js'])

    <wireui:scripts />

    @livewireStyles

    @yield('customStyles')

</head>

<body class="dark:bg-black w-full sm:w-3/4 md:w-2/3 lg:w-2/3 xl:w-2/3 aspect-[9/16] bg-gray-200 mx-auto">          

    <x-notifications />

    <div id="header"> @yield('header') </div>
    
    <div class="text-2xl text-green-950 font-bold text-center py-2 my-2">
        Asistente
    </div>

    <div id="main" class="py-1"> @yield('main') </div>

    <div id="footer"> @yield('footer') </div>

    @yield('customScripts')

    @livewireScripts

    @yield('scriptsLivewire')

</body>

</html>