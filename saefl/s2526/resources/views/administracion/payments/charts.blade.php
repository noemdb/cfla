@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                <h4>
                    <i class="{{ $icon_menus['chartline'] ?? '' }}"></i>
                    Análisis gráfico de los <b>Reportes de Pagos</b> diarios registrados por los representantes
                </h4>
                <span class="text-muted float-right">Puede ser usado para mostrar el impacto que puedan tener políticas de cobro implementadas en un lapso de tiempo determinado</span>
            </div>

            <div class="card-body">
                {{-- Mensaje session-flash sobre operaciones con base de datos --}}

                @include('administracion.payments.charts.countxday')

            </div>
        </div>
    </main>

@endsection

@section('scripts') @parent <script type="text/javascript"> document.title = 'SAEFL - Reportes de Pago, listado'; </script> @endsection

@section('scripts')
    @parent
    {{-- <script src="{{ asset("js/Chart.js") }}"></script> --}}
    <script src="{{ asset("js/Chart.bundle.js") }}"></script>
    {{-- <script src="{{ asset("js/utils.js") }}"></script> --}}
    <script src="{{ asset("js/ChartFunction.js") }}"></script>{{-- Funciones para generar los Chart --}}

    {{-- INI Evento clic para generar los Chart por rango--}}
    <script src="{{ asset("js/ChartEvent.js") }}"></script>{{-- Funciones para generar los Chart --}}
    {{-- FIN Evento clic para generar los Chart por rango --}}

@endsection
