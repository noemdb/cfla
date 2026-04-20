<html class="scroll-smooth">

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
    class="bg-[#020617] text-gray-100 font-sans antialiased min-h-screen bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-emerald-950/30 via-gray-950 to-black selection:bg-emerald-500 selection:text-white bg-fixed flex flex-col">
    <x-notifications />
    <div id="header" data-header="header" class="relative z-50"> @yield('header') </div>
    <div id="main" class="flex-1 relative z-10">
        <main class="container-fluid mx-auto px-4 py-8">
            @yield('main')
        </main>
    </div>
    <div id="footer" class="relative z-50 mt-auto"> @yield('footer') </div>
    @yield('javascripts')
    @livewireScripts
</body>

</html>
