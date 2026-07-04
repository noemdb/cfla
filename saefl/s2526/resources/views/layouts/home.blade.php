<!DOCTYPE html>

<html lang="{{ app()->getLocale() }}">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- <meta name="google-site-verification" content="LEr-TNoeOkRTwIle6YTuKbubiYWuZve7HM3iiPqfvn0" /> --}}
    <meta name="google-site-verification" content="ka1CAY1FPMguPoTJohq1H98igxL6N1qSnHSWDX9agzg" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Inicio</title>

    <!-- Styles -->
    <link href="{{ asset('vendor/bootstrap/4.1.2/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('css/cover.css') }}" rel="stylesheet">
    <link href="{{ asset('css/docs.min.css') }}" rel="stylesheet">
    {{-- <link href="{{ asset('css/floating-labels.css') }}" rel="stylesheet"> --}}
    {{-- <script defer src="{{ asset('vendor/fontawesome/5.0.8/svg-with-js/js/fontawesome-all.js') }}"></script> --}}
    <script defer src="{{ asset('vendor/fontawesome/5.10.1/js/all.js') }}"></script>

</head>

<body class="text-center">

    <div class="cover-container d-flex justify-content-center h-100 p-3 mx-auto flex-column">

        <header class="masthead mb-auto">

            <div class="inner small">

                {{-- <h3 class="masthead-brand">{{ config('app.name', 'Laravel') }}</h3> --}}

                @include('layouts.partials.navbar.home')

            </div>

        </header>

        @yield('content')

        @include('layouts.footer.home')

        {{--
    @if (Route::has('login'))
        <div class="top-right links">
            @auth
                <a href="{{ url('/home') }}">Inicio</a>
            @else
                <a href="{{ url('/') }}">Inicio</a>
            @endauth
        </div>
    @endif
    --}}

        <!-- Scripts -->
        <script src="{{ asset('vendor/jquery/3.2.1/slim/jquery.js') }}"></script>
        <script src="{{ asset('vendor/ajax/popper/1.12.9/js/popper.js') }}"></script>
        <script src="{{ asset('vendor/bootstrap/4.0.0/js/bootstrap.js') }}"></script>

</body>

</html>
