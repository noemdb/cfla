<!-- resources/views/layouts/app.blade.php -->

<html>

<head>
    <title>{{env('APP_NAME')}} - @yield('title')</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite('resources/css/app.css')
    @vite(['resources/js/app.js'])

    @livewireStyles

    @wireUiScripts

</head>

<body>

    <livewire:home />

    <div id="header"> @yield('header') </div>

    <div id="aside" class="p-2"> @yield('aside') </div>
    
    <div id="hero" class="p-2"> @yield('hero') </div>    

    <div id="highlighted" class="p-2"> @yield('highlighted') </div>

    <div id="featured" class="p-2"> @yield('featured') </div>

    <div id="category" class="p-2"> @yield('category') </div>

    <div id="gallery" class="p-2"> @yield('gallery') </div>

    <div id="autority" class="p-2"> @yield('autority') </div>

    <div id="testimonials" class="p-2"> @yield('testimonials') </div>

    <div id="timeline" class="p-2"> @yield('timeline') </div>

    <div id="contacs" class="p-2"> @yield('contacs') </div>

    
    <div id="container" class="p-2"> @yield('content') </div>

    <div id="footer"> @yield('footer') </div>

    @yield('scripts')

</body>

</html>