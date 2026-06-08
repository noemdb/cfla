@extends('inicials.layouts.dashboard.app')

@section('main')

    <main role="main" class="d-flex vh-100">

        <div class="container my-auto">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8">
                    <div class="jumbotron text-center bg-white">
                        <h1 class="display-4 font-weight-bold">Educación Inicial</h1>
                        <p class="lead text-muted">Formatos para la Planificación y Evaluación.</p>
                        <hr class="my-4 font-weight-bold">
                        <p>Planificación Semanal, Planificación Quincenal, Proyecto de Aula, Plan Especial, Informe Pedagógico, Plan de Evaluación e Informe Final</p>
                    </div>
                </div>
            </div>
        </div>

    </main>

@endsection

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

