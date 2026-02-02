<!-- resources/views/layouts/app.blade.php -->

<html class="scroll-smooth dark" style="background: none;">

<head>

    <title>@yield('title')</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite('resources/css/app.css')
    @vite(['resources/js/app.js'])

    <wireui:scripts />
    @livewireStyles

</head>

<body
    class="bg-[#020617] text-gray-100 font-sans antialiased min-h-screen flex justify-center items-start pt-0 sm:pt-6 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-emerald-950/50 via-gray-950 to-black selection:bg-emerald-500 selection:text-white bg-fixed">

    <x-notifications />

    <!-- Mobile App Container -->
    <div
        class="w-full h-full max-w-md bg-gray-950/70 backdrop-blur-xl supports-[backdrop-filter]:bg-gray-950/40 min-h-screen sm:min-h-[calc(100vh-3rem)] sm:h-auto sm:rounded-3xl shadow-[0_20px_50px_-12px_rgba(16,185,129,0.15)] border-x border-gray-800/50 sm:border border-white/5 relative flex flex-col overflow-hidden ring-1 ring-white/10">

        <!-- Header Section -->
        <div id="header" class="bg-gray-900/50 backdrop-blur-md sticky top-0 z-50 border-b border-white/5">
            @yield('header')
            <div class="px-4 pb-3">
                <div
                    class="text-center py-2 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 font-bold text-sm tracking-wide shadow-sm">
                    U.E. Colegio Fray Luis Amigó
                </div>
            </div>
        </div>

        <!-- Main Content (Scrollable) -->
        <div id="main" class="flex-1 overflow-y-auto custom-scrollbar p-4 space-y-4">
            @yield('main')
        </div>

        <!-- Footer -->
        <div id="footer" class="bg-gray-950/50 border-t border-white/5 z-10 backdrop-blur-sm">
            @yield('footer')
        </div>

    </div>

    @yield('customScripts')

    @livewireScripts


    @yield('scriptsLivewire')

</body>

</html>
