<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Inicio - ADMIN</title>

    <!-- Styles -->
    <link href="{{ asset('vendor/bootstrap/4.1.2/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('css/docs.min.css') }}" rel="stylesheet">

    <link href="{{ asset('vendor/fontawesome/5.2.0/css/all.css') }}" rel="stylesheet">

    <!-- stylesheet for page -->
    @yield('stylesheet')

    <!-- Scripts -->
    <script>
        window.Laravel = {{ json_encode(['csrfToken' => csrf_token(),]) }}
    </script>

</head>
<body>

    <!-- content for page -->
    @yield('body')

    <!-- footer for page -->
    @yield('footer')

    @include('layouts.footer.home')

    <!-- Scripts -->
    <script src="{{ asset('vendor/jquery/3.3.1/jquery.js') }}"></script>
    <script src="{{ asset('vendor/ajax/popper/1.12.9/js/popper.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/4.1.2/js/bootstrap.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert/7.26.9/js/sweetalert2.all.min.js') }}"></script>

    <!-- scripts for page -->
    @yield('scripts')


    <!-- scripts for page -->
    @yield('scriptsCustome')

</body>
</html>
