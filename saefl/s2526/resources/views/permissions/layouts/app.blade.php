<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ config('app.name', 'Laravel') }} -Pases - {{ Auth::user()->area ?? '' }} - {{ Auth::user()->rol ?? '' }}</title>

    <link href="{{ asset('vendor/bootstrap/4.3.1/css/bootstrap.css') }}" rel="stylesheet">

</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                @yield('content')
            </div>
        </div>
    </div>

    @yield('footer')

    <script src="{{ asset('vendor/jquery/3.3.1/jquery.js') }}"></script>
    <script src="{{ asset('vendor/ajax/popper/1.14.7/js/popper.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/4.3.1/js/bootstrap.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert/8.17.6/js/sweetalert2.all.min.js') }}"></script>
</body>
</html>
