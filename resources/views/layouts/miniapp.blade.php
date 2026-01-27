<!-- resources/views/layouts/app.blade.php -->

<html class="scroll-smooth dark">

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
    class="bg-black text-gray-100 font-sans antialiased min-h-screen flex justify-center items-start pt-0 sm:pt-6 bg-gradient-to-br from-gray-900 to-black">

    <x-notifications />

    <!-- Mobile App Container -->
    <div
        class="w-full max-w-md bg-gray-900 min-h-screen sm:min-h-[calc(100vh-3rem)] sm:h-auto sm:rounded-3xl shadow-2xl border-x border-gray-800 sm:border border-emerald-500/20 relative flex flex-col overflow-hidden">

        <!-- Header Section -->
        <div id="header" class="bg-gray-900/50 backdrop-blur-md sticky top-0 z-50 border-b border-emerald-500/20">
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
        <div id="footer" class="bg-gray-900 border-t border-emerald-500/20 z-10">
            @yield('footer')
        </div>

    </div>

    @yield('customScripts')

    @livewireScripts


    @yield('scriptsLivewire')

</body>

</html>
