<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap CSS -->
    <link href="{{ asset('vendor/bootstrap/5.3.0/css/bootstrap.css') }}" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="{{ asset('vendor/fontawesome/5.2.0/css/all.css') }}" rel="stylesheet">

    <!-- Livewire Styles -->
    @livewireStyles

    @stack('styles')
</head>

<body>

    <div class="container-fluid">

        @include('layouts.navigation')

        <main class="py-4">
            @yield('content')
        </main>

    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="{{ asset('vendor/bootstrap/5.3.0/js/bootstrap.js') }}"></script>

    <script src="{{ asset('vendor/apexcharts/4.7.0/apexcharts.js') }}"></script>

    <!-- Livewire Scripts -->
    @livewireScripts

    <!-- scripts for page [stack] -->
    @stack('scripts')

    <!-- scripts for page -->
    @yield('scripts')

</body>
</html>
