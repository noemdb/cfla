@extends('administracion.layouts.dashboard.app')

@section('title') Tasas de cambios registradas - {{ Auth::user()->rol ?? '' }} @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">

                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.configuraciones.exchange_rates.menus.index')
                </div>
                {{-- FIN Menu rapido --}}

                <h3><span class="font-weight-bolder">Tasas de cambios</span> registradas</h3>

            </div>

            <div class="card-body">

                @include('administracion.elements.forms.errors')
                @include('administracion.elements.messeges.oper_ok')

                <div class="row">
                    <div class="col-sm-12">
                        @include('administracion.configuraciones.exchange_rates.table.index')
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-12">
                        @include('administracion.configuraciones.exchange_rates.chart.movimientocambiario')
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
