@extends('administracion.layouts.dashboard.app')

@section('title') SAEFL - Autorrespondedor @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    {{-- @include('administracion.edescriptivas.menus.index') --}}
                </div>
                {{-- FIN Menu rapido --}}
                <h4>Listado <span class="font-weight-bolder">de Mensajes recibidos</span> registrados</h4>
            </div>

            <div class="card-body">

                @include('administracion.autoresponders.bmesseges.table.index')

                <hr>

                <div class="alert alert-secondary font-weight-bold">
                    ¿Cauntas veces se consultan las opciones disponibles?
                </div>

                @include('administracion.autoresponders.bmesseges.charts.options')
                {{-- views/administracion/autoresponder/table/index.blade.php --}}

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

