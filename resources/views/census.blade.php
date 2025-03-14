<!DOCTYPE html>
<html class="dark" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Censo Escolar CFLA - {{ config('app.name') }}</title>

    <!-- Livewire -->
    @livewireStyles

    @wireUiScripts

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @yield('styles')
    
</head>
<body>

    <x-notifications />

    <livewire:enrollment-wizard  />    

    @livewireScripts

    @yield('scripts')

</body>

</html>

