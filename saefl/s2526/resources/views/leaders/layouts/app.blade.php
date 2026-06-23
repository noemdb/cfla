<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - JEFE DE ÁREA</title>

    <!-- Styles -->
    <link href="{{ asset('vendor/bootstrap/4.3.1/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('css/docs.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/adminlte.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/fontawesome/5.2.0/css/all.css') }}" rel="stylesheet">

    <!-- stylesheet for page -->
    @yield('stylesheet')

    <style>
        .overlay {
            position: absolute;
            width: 70%;
            /* height: 80vh; */
            background-color: rgba(240, 240, 240, 1);
            z-index: 999;
            /* display: flex; */
            justify-content: center;
            /* align-items: center; */
            border-radius: 0.5rem; 
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.3), 0px 10px 20px rgba(0, 0, 0, 0.5);
            margin-left: 1rem;
            margin-right: 1rem;
            /* opacity: 0.8; */
        }     
    </style>

    <!-- Scripts -->

    @livewireStyles

</head>
<body>

    <!-- content for page -->
    @yield('body')

    <!-- footer for page -->
    @yield('footer')

    <!-- Scripts -->   

    <script src="{{ asset('vendor/jquery/3.3.1/jquery.js') }}"></script>
    <script src="{{ asset('vendor/ajax/popper/1.14.7/js/popper.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/4.3.1/js/bootstrap.js') }}"></script>

    <!-- charjs for page -->
    @yield('chartjs')

    <!-- sweetalert for page -->
    <script src="{{ asset('vendor/sweetalert/8.17.6/js/sweetalert2.all.min.js') }}"></script>
    @yield('sweetalert')

    <!-- scripts for page -->
    {{-- @yield('scripts') --}}

    @livewireScripts    

</body>
</html>
