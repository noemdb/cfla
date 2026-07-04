@extends('administracion.layouts.dashboard.app')

@section('title') Lapsos/Censo, listado participantes @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.configuraciones.lapsos.menus.census')
                </div>
                {{-- FIN Menu rapido --}}
                <h4>
                    <u title="Indicadores">Indicadores</u> del proceso de  <span class="font-weight-bolder">Registros de participantes del censo escolar</span> 
                    <span class="small font-weight-bold">[{{$lapso->name ?? null}}]</span>
                </h4>
            </div>

            <div class="card-body">                

                <div class="row">
                    <div class="col-12">
                        @include('administracion.configuraciones.lapsos.chart.institution')
                    </div>
                    <div class="col-12">
                        @include('administracion.configuraciones.lapsos.chart.grados')
                    </div>
                    <div class="col-12">
                        @include('administracion.configuraciones.lapsos.chart.municipio')
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="col">
                        @include('administracion.configuraciones.lapsos.chart.grados')
                    </div>
                </div> --}}

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