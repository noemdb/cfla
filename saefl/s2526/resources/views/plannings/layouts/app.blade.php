<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - JEFE MÓDULO DE PLANIFICACIÓN</title>

    <!-- Styles -->
    {{-- <link href="{{ asset('vendor/bootstrap/4.1.2/css/bootstrap.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('vendor/bootstrap/4.3.1/css/bootstrap.css') }}" rel="stylesheet">
    {{-- <link href="{{ asset('css/cover.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('css/docs.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/adminlte.css') }}" rel="stylesheet">

    {{-- <link href="{{ asset('css/floating-labels.css') }}" rel="stylesheet"> --}}
    {{-- <link href="{{ asset('vendor/fontawesome/5.0.8/css/fontawesome-all.css') }}" rel="stylesheet"> --}}
    {{-- <script defer src="{{ asset('vendor/fontawesome/5.2.0/svg-with-js/js/fontawesome-all.js') }}"></script> --}}
    <link href="{{ asset('vendor/fontawesome/5.2.0/css/all.css') }}" rel="stylesheet">
    {{-- <script defer src="{{ asset('vendor/fontawesome/5.2.0/js/all.js') }}"></script> --}}

    {{-- <link href="{{ asset('vendor/toastr/2.1.4/css/toastr.css') }}" rel="stylesheet"> --}}

    {{-- <link href="{{ asset('vendor/sweetalert/7.26.9/css/sweetalert2.min.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('vendor/sweetalert/9.10.12/css/theme-bootstrap-4.css') }}" rel="stylesheet">

    <link href="{{ asset('css/timeline.css') }}" rel="stylesheet">

    <!-- stylesheet for page -->
    @yield('stylesheet')

    <style>
        .overlay-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 998;
            /* display: none; */
        }

        .overlay {
            position: absolute;
            background-color: rgba(240, 240, 240, 1);
            z-index: 999;
            border-radius: 0.5rem;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.3), 0px 10px 20px rgba(0, 0, 0, 0.5);
            /* box-shadow: 100px 100px 0px rgba(0, 0, 0, 0.5); */
            margin-left: 1rem;
            margin-right: 1rem;
            padding: 1rem;
        }

        .show-overlay .overlay-background,
        .show-overlay .overlay {
            display: block;
        }

        .badge-lg {
            font-size: 1em;
            padding: 0.2em 0.4em;
        }
    </style>



    <!-- livewireStyles -->
    @livewireStyles

    <!-- Scripts -->

</head>

<body>

    <!-- content for page -->
    @yield('body')

    <!-- footer for page -->
    @yield('footer')

    <!-- Scripts -->
    <script src="{{ asset('vendor/jquery/3.3.1/jquery.js') }}"></script>
    <script src="{{ asset('vendor/ajax/popper/1.14.7/js/popper.js') }}"></script>
    {{-- <script src="{{ asset('vendor/ajax/popper/1.12.9/js/popper.js') }}"></script> --}}
    <script src="{{ asset('vendor/bootstrap/4.3.1/js/bootstrap.js') }}"></script>
    {{-- <script src="{{ asset('vendor/bootstrap/4.1.2/js/bootstrap.js') }}"></script> --}}

    {{-- <script src="{{ asset('vendor/toastr/2.1.4/js/toastr.min.js') }}"></script> --}}

    {{-- <script src="{{ asset('vendor/sweetalert/7.26.9/js/sweetalert2.all.min.js') }}"></script> --}}

    {{-- <script src="{{ asset('vendor/sweetalert/8.17.6/js/sweetalert2.all.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('vendor/sweetalert/9.10.12/js/sweetalert2.all.min.js') }}"></script> --}}

    <script src="{{ asset('vendor/alpine/3.11.1/cdn.min.js') }}" defer></script>

    <script src="{{ asset('vendor/sweetalert/11.4.8/js/sweetalert2.all.min.js') }}"></script>
    @yield('sweetalert')

    <!-- scripts for page -->
    @yield('scripts')

    <!-- livewireScripts -->
    @livewireScripts

</body>

</html>
