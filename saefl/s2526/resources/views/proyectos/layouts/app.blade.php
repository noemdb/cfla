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

    <!-- stylesheet for page -->
    @yield('stylesheet')

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
    <script src="{{ asset('vendor/bootstrap/4.3.1/js/bootstrap.js') }}"></script>

    <script src="{{ asset('vendor/sweetalert/8.17.6/js/sweetalert2.all.min.js') }}"></script>

    <!-- Inactivity Timeout Script -->
    <script>
        // Configuración del tiempo de inactividad (en minutos)
        const INACTIVITY_TIMEOUT = 15; // 15 minutos
        let inactivityTimer;

        // Función para reiniciar el temporizador
        function resetInactivityTimer() {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(handleInactivity, INACTIVITY_TIMEOUT * 60 * 1000);
        }

        // Función para manejar la inactividad
        function handleInactivity() {
            Swal.fire({
                title: 'Sesión por expirar',
                text: 'Su sesión ha estado inactiva por un tiempo prolongado. ¿Desea continuar?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'No, cerrar sesión',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    resetInactivityTimer();
                } else {
                    window.location.href = '/';
                }
            });
        }

        // Eventos que reinician el temporizador
        const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'];
        events.forEach(event => {
            document.addEventListener(event, resetInactivityTimer);
        });

        // Iniciar el temporizador cuando se carga la página
        resetInactivityTimer();
    </script>

    <!-- scripts for page -->
    @yield('scripts')

    @yield('charjs')

</body>
</html>
