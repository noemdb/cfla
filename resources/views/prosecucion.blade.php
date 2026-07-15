<!DOCTYPE html>
<html class="dark" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Prosecución Estudiantil · {{ config('app.name') }}</title>

    @livewireStyles
    @wireUiScripts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')

    <style>
        /* Asegurar que las notificaciones toast estén por encima del layout de dos paneles */
        .fixed.inset-0.z-70 {
            z-index: 9999 !important;
        }
    </style>

</head>

<body>

    <x-notifications />

    @livewire('prosecucion-wizard')

    @livewireScripts
    @yield('scripts')

</body>
</html>
