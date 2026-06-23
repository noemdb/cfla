<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - {{ Auth::user()->area ?? '' }} - {{ Auth::user()->rol ?? '' }}</title>

    <!-- Styles -->
    <link href="{{ asset('vendor/bootstrap/4.3.1/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('css/docs.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/adminlte.css') }}" rel="stylesheet">

    <link href="{{ asset('vendor/fontawesome/5.2.0/css/all.css') }}" rel="stylesheet">


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



    <style>
        /* Botón flotante */
        .floating-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1050;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        /* Modal ancho completo */
        .modal-dialog.modal-xl {
            max-width: 95% !important;
            /* ocupa casi todo el ancho */
        }
    </style>

    <!-- livewireStyles -->
    @livewireStyles

    <!-- Scripts -->

</head>

<body>

    <!-- content for page -->
    @yield('main')

    <!-- footer for page -->
    @yield('footer')

    @include('administracion.instructions.modals')

    <!-- Scripts -->
    <script src="{{ asset('vendor/jquery/3.3.1/jquery.js') }}"></script>
    <script src="{{ asset('vendor/ajax/popper/1.14.7/js/popper.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/4.3.1/js/bootstrap.js') }}"></script>

    {{-- <script src="{{ asset('vendor/alpine/3.11.1/cdn.min.js') }}" defer></script> --}}

    {{-- <script src="{{ asset('vendor/sweetalert/11.4.8/js/sweetalert2.all.min.js') }}"></script> --}}
    @yield('sweetalert')

    <!-- scripts for page -->
    @yield('scripts')

    <!-- livewireScripts -->
    @livewireScripts

</body>

</html>
