<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link href="{{ asset('vendor/bootstrap/4.1.2/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('css/welcome.css') }}" rel="stylesheet">

    {{-- <script src="{{ asset('js/alpine/3.x.x/cdn.min.js') }}" defer></script> --}}

</head>

<body>
    <div class="flex-center position-ref full-height">
        @if (Route::has('login'))
            <div class="top-right links">

                @auth
                    <a href="{{ url('/home') }}" style="color:#004000">Inicio</a>
                @else
                    <a href="{{ route('login') }}" style="color:#004000">Ingresar</a>
                    {{-- <a href="{{ route('register') }}">Registrar</a> --}}
                @endauth

            </div>
        @endif

        <div class="content">
            @switch(env('APP_NAME'))
                @case('SAEFL')
                    @php($color = '#004000')
                @break

                @case('SAEFL.DEV')
                    @php($color = '#FF0000')
                @break

                @case('SAE')
                    @php($color = '#004000')
                @break

                @default
                    @php($color = '#004000')
            @endswitch

            <div class="h6 font-weight-bold m-0 p-0 text-secondary text-uppercase">Período Escolar 2025 2026</div>

            <div class="title m-b-md" style="color:{{ $color ?? '#FF0000' }}">
                <span class="font-weight-bold">{{ config('app.name', 'Laravel') }}</span>
            </div>


            <div class="px-2">

                <ul class="nav nav-tabs nav-fill font-weight-bold text-muted" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="name-tab" data-toggle="tab" href="#name" role="tab"
                            aria-controls="name" aria-selected="true">Nombre</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="home-tab" data-toggle="tab" href="#home" role="tab"
                            aria-controls="home" aria-selected="true">Descripción</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab"
                            aria-controls="profile" aria-selected="false">Funcionalidades</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab"
                            aria-controls="contact" aria-selected="false">Módulos</a>
                    </li>
                </ul>

                <div class="tab-content links" id="myTabContent">
                    <div class="tab-pane fade active show" id="name" role="tabpanel" aria-labelledby="name-tab">
                        <div class="p-4 text-center text-muted font-weight-normal text-uppercase">
                            Sistema Automatizado para la Gestión Escolar Fray Luis Amigó
                        </div>
                    </div>
                    <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <div class="p-4 text-left">
                            Se trata de un sistema automatizado, diseñado con tecnologías web de vanguardia. Amigable
                            con el usuario, compatible con múltiples dispositivos y plataformas, sostenible,
                            actualizable y escalable. De código abierto, lo que garantiza una rápida implementación y
                            eficiencia operativa, junto con altos niveles de seguridad para la protección de la
                            información. Puede ser implementado en Internet, a través de una dirección web específica,
                            permitiendo el acceso desde cualquier dispositivo conectado a la red.
                        </div>
                    </div>
                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="p-4 text-left">
                            Nuestra plataforma ofrece una solución efectiva para la organización, asignación y ejecución
                            de los planes de evaluación. Con la capacidad de establecer y gestionar los indicadores y
                            momentos de evaluación, nuestro sistema facilita la planificación y seguimiento del proceso
                            educativo. Esto significa que los educadores pueden enfocarse en la educación de los
                            estudiantes, mientras que nuestro sistema se encarga de manejar las tareas administrativas.
                        </div>
                        <div class="p-4 text-left">
                            Cuenta con una amplia gama de funcionalidades diseñadas para facilitar la gestión integral
                            de las instituciones educativas. Entre ellas, destacan los reportes financieros detallados,
                            una estructura amplia para la recepción y registro de pagos, políticas de cobranza altamente
                            eficaces y un canal de comunicación directa. Con estas herramientas, nuestro sistema ofrece
                            un control riguroso y eficiente de los ingresos por concepto de escolaridad de la
                            institución, permitiendo mantener una situación económica estable y tomar decisiones
                            informadas sobre los gastos e inversiones necesarios.
                        </div>
                    </div>
                    <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">

                        <div class="p-2 text-right">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item py-1">Configuraciones</li>
                                <li class="list-group-item py-1">Estudiantes</li>
                                <li class="list-group-item py-1">Representantes</li>
                                <li class="list-group-item py-1">Gestión de Procesos de Consultas</li>
                                <li class="list-group-item py-1">Procesamiento y Registro de Pagos</li>
                                <li class="list-group-item py-1">Inscripciones Administrativas</li>
                                <li class="list-group-item py-1">Inscripciones Académicas</li>
                                <li class="list-group-item py-1">Control de Asistencia</li>
                                <li class="list-group-item py-1">Plan de Evaluación</li>
                                <li class="list-group-item py-1">Histórico de Notas</li>
                                <li class="list-group-item py-1">Promociones y Títulos</li>
                                <li class="list-group-item py-1">Gestión de Envío de correos electrónicos</li>
                            </ul>
                        </div>

                    </div>
                </div>

            </div>

            {{-- <div class="links">
                    <a href="#">Descric</a>
                    <a href="#">Demostración</a>
                    <a href="#">GitHub</a>
                    <a href="#">Institución</a>
                </div> --}}

        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('vendor/jquery/3.3.1/jquery.js') }}"></script>
    <script src="{{ asset('vendor/ajax/popper/1.12.9/js/popper.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/4.1.2/js/bootstrap.js') }}"></script>
</body>

</html>
