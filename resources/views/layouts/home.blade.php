<!-- resources/views/layouts/app.blade.php -->

<html class="scroll-smooth">

<head>
    {{-- <title>{{env('APP_NAME')}} - @yield('title')</title> --}}
    <title>@yield('title')</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- @vite('resources/css/app.css') --}}
    {{-- @vite(['resources/js/app.js']) --}}

    {{-- <wireui:scripts /> --}}
    @livewireStyles

    <style>
        .truncate-sm {
            @apply text-overflow-ellipsis sm:hidden;
        }
    </style>

</head>

<body class="bg-white dark:bg-black">

    {{-- <x-notifications /> --}}

    <livewire:home />

    {{-- <div id="header"> @yield('header') </div> --}}

    {{-- <div id="aside"> @yield('aside') </div> --}}

    {{-- <div id="hero" class="p-1"> @yield('hero') </div> --}}

    {{-- <div id="highlighted" class="p-1"> @yield('highlighted') </div> --}}

    {{-- <div id="featured" class="p-1"> @yield('featured') </div> --}}

    {{-- <div id="category" class="p-1"> @yield('category') </div> --}}

    {{-- <div id="gallery" class="p-1"> @yield('gallery') </div> --}}

    {{-- <div id="services" class="p-1"> @yield('services') </div> --}}

    {{-- <div id="alliances" class="p-1"> @yield('alliances') </div> --}}

    {{-- <div id="autority" class="p-1"> @yield('autority') </div> --}}

    {{-- <div id="workers" class="p-1"> @yield('workers') </div> --}}

    {{-- <div id="testimonials" class="p-1"> @yield('testimonials') </div> --}}

    {{-- <div id="timeline" class="p-1"> @yield('timeline') </div> --}}

    {{-- <div id="graphs" class="p-1"> @yield('graphs') </div> --}}

    {{-- <div id="contacts" class="p-1"> @yield('contacts') </div> --}}

    {{-- <div id="socials" class="p-1"> @yield('socials') </div> --}}

    {{-- <div id="container" class="p-1"> @yield('content') </div> --}}

    {{-- <div id="footer"> @yield('footer') </div> --}}

    {{-- @yield('scripts') --}}

    @livewireScripts

</body>

</html>