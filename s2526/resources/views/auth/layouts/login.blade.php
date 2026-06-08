<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('vendor/bootstrap/4.3.1/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
    <link href="{{ asset('css/floating-labels.css') }}" rel="stylesheet">
    <script defer src="{{ asset('vendor/fontawesome/5.0.8/svg-with-js/js/fontawesome-all.js') }}"></script>

</head>
<body>
    <div class="d-flex justify-content-center">
        @if (Route::has('login'))
            <div class="top-right links">
                @auth
                    <a href="{{ url('/home') }}">Inicio</a>
                @else
                    <a href="{{ url('/') }}">Inicio</a>
                @endauth
            </div>
        @endif

        @yield('content')

        <!-- Scripts -->
        <script src="{{ asset('vendor/jquery/3.2.1/slim/jquery.js') }}"></script>
        {{-- <script src="{{ asset('vendor/ajax/popper/1.12.9/js/popper.js') }}"></script> --}}
        <script src="{{ asset('vendor/bootstrap/4.0.0/js/bootstrap.js') }}"></script>
    </div>

    <!-- scripts for page -->
    @yield('scripts')

</body>
</html>
