<!-- resources/views/layouts/app.blade.php -->

<html class="scroll-smooth">

<head>
    {{-- <title>{{env('APP_NAME')}} - @yield('title')</title> --}}
    <title>@yield('title')</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite('resources/css/app.css')
    @vite(['resources/js/app.js'])

    <wireui:scripts />
    @livewireStyles

    <style>
        .truncate-sm {
            @apply text-overflow-ellipsis sm:hidden;
        }
    </style>

</head>

<body class="bg-white dark:bg-black">

    {{-- @livewire('order-status-notification') --}}

    {{-- <x-notifications /> --}}

    <x-notifications position="top-center" />

    {{-- <livewire:home /> --}}

    <div id="header"> @yield('header') </div>

    {{-- <div id="aside"> @yield('aside') </div> --}}

    <div id="hero" class="p-0"> @yield('hero') </div>

    <div id="highlighted" class="p-0"> @yield('highlighted') </div>

    <div id="featured" class="p-0"> @yield('featured') </div>

    {{-- <div id="category" class="p-0"> @yield('category') </div> --}}

    

    <div id="services" class="p-0"> @yield('services') </div>

    {{-- <div id="alliances" class="p-0"> @yield('alliances') </div> --}}

    

    <div id="testimonials" class="p-0"> @yield('testimonials') </div>

    <div id="gallery" class="p-0"> @yield('gallery') </div>

    <div id="autority" class="p-0"> @yield('autority') </div>

    <div id="workers" class="p-0"> @yield('workers') </div>

    <div id="contacts" class="p-0"> @yield('contacts') </div>

    <div id="socials" class="p-0"> @yield('socials') </div>

    

    {{-- <div id="container" class="p-0"> @yield('content') </div> --}}

    {{-- <div id="timeline" class="p-0"> @yield('timeline') </div> --}}

    {{-- <div id="graphs" class="p-0"> @yield('graphs') </div> --}}

    

    

    <div id="footer"> @yield('footer') </div>

    @yield('customScripts')

    @livewireScripts


    @yield('scriptsLivewire')

    {{-- @livewireScriptConfig --}}

</body>

</html>