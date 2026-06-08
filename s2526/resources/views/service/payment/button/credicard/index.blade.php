<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }} - Boton de pago Credicard</title>

    <link href="{{ asset('css/welcome.css') }}" rel="stylesheet">

    <link href="{{ asset('vendor/bootstrap/5.3.0/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap/5.3.0/icon/bootstrap-icons.css') }}" rel="stylesheet">

    <link href="{{ asset('vendor/stepper/css/all.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/stepper/css/bs-stepper.min.css') }}" rel="stylesheet">

    <link href="{{ asset('css/docs.min.css') }}" rel="stylesheet">

    @livewireStyles

</head>

<body class="d-flex flex-column min-vh-100 bg-light" style="background-image: url('{{asset('images/background/service/credicard_opacity.png')}}');">

    <div class="flex-center position-ref">

        <div class="content border rounded p-2 m-2 bg-white shadow" >

            <center>
                <img class="mb-0" src="{{ asset('images/brand/144/1.png') }}" alt="" width="144" height="144">
            </center>

            <div class="container mt-5">
                <div class="alert alert-warning text-center" role="alert">
                    <h4 class="alert-heading">⚠️ Importante: Servicio Suspendido Temporalmente</h4>
                    <p>Estimado usuario, le informamos que el servicio de <strong>Botón de Pago Virtual (TPV)</strong> se encuentra temporalmente suspendido.</p>
                    <hr>
                    <p class="mb-0">Agradecemos su comprensión y le pedimos disculpas por cualquier inconveniente.</p>
                </div>
            </div>

            {{-- <livewire:service.payment.button.credicard.index-component /> --}}

        </div>

    </div>

    <script src="{{ asset('vendor/alpine/3.11.1/cdn.min.js') }}" defer></script>

    <script src="{{ asset('vendor/bootstrap/5.3.0/js/bootstrap.bundle.min.js') }}"></script>

    @livewireScripts

</body>

</html>
