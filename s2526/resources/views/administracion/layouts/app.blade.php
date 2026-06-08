<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} @yield('title') </title>

    <!-- Styles -->
    <link href="{{ asset('vendor/bootstrap/4.3.1/css/bootstrap.css') }}" rel="stylesheet">
    
    <link href="{{ asset('css/docs.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/adminlte.css') }}" rel="stylesheet">
    <link href="{{ asset('css/form.css') }}" rel="stylesheet">
    <link href="{{ asset('css/helpers.css') }}" rel="stylesheet">

    <link href="{{ asset('vendor/fontawesome/5.15.4/css/all.min.css') }}" rel="stylesheet">

    <link href="{{ asset('vendor/sweetalert/9.10.12/css/theme-bootstrap-4.css') }}" rel="stylesheet">

    <!-- stylesheet for page -->
    @yield('stylesheet')

    <!-- livewireStyles -->
    @livewireStyles

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
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        }

         /* Modal ancho completo */
        .modal-dialog.modal-xl {
            max-width: 95% !important; /* ocupa casi todo el ancho */
        }
    </style>

</head>
<body>

    <!-- content for page -->
    @yield('body')

    @include('administracion.instructions.modals')
    @yield('modals')

    <!-- footer for page -->
    @yield('footer')

    <!-- Scripts -->
    <script src="{{ asset('vendor/jquery/3.3.1/jquery.js') }}"></script>
    <script src="{{ asset('vendor/ajax/popper/1.14.7/js/popper.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/4.3.1/js/bootstrap.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert/11.4.8/js/sweetalert2.all.min.js') }}"></script>

    <!-- scripts for page -->
    @yield('scripts')

    <!-- livewireScripts -->
    @livewireScripts

    <!-- livewireCustomeScripts -->
    @yield('livewireCustomeScripts')

    @yield('sweetalert')
    
</body>
</html>
