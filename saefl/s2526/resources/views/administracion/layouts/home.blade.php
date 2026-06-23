<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} @yield('title') </title>

    <!-- Styles -->
    <link href="{{ asset('vendor/bootstrap/4.1.2/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('css/docs.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/adminlte.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/fontawesome/5.2.0/css/all.css') }}" rel="stylesheet">
    <link href="{{ asset('css/helpers.css') }}" rel="stylesheet">

    <!-- stylesheet for page -->
    @yield('stylesheet')

    <!-- livewireStyles -->
    @livewireStyles

    <style>
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

        .modal-dialog.modal-xl {
            max-width: 95% !important;
        }
        
        /* Loading spinner */
        .content-loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
        }
    </style>
</head>
<body>

    <!-- content for page -->
    @yield('body')

    <!-- footer for page -->
    @yield('footer')

    @include('administracion.instructions.modals')

    <!-- Scripts -->
    <script src="{{ asset('vendor/jquery/3.3.1/jquery.js') }}"></script>
    <script src="{{ asset('vendor/ajax/popper/1.12.9/js/popper.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/4.1.2/js/bootstrap.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert/7.26.9/js/sweetalert2.all.min.js') }}"></script>

    <!-- scripts for page -->
    @yield('scripts')

    <!-- livewireScripts -->
    @livewireScripts    

</body>
</html>