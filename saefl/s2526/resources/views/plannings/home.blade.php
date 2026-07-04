@extends('plannings.layouts.home.app')

@section('main')

    <main role="main" class="d-flex vh-100">

        <div class="container my-auto">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8">
                    <div class="jumbotron text-center bg-white">                       

                        <div class="container-fluid">
                            <div class="row align-items-center">
                                <div class="col-4 d-flex justify-content-center">
                                    <div>
                                        @include('plannings.icons.svg')                             
                                    </div>
                                </div>
                                <div class="col-8 text-left">
                                    <h1 class="display-4 font-weight-bold">Módulo de Planificación</h1>
                                    <p class="text-muted">Gestión para la <b>Planificación de Actividades Docentes.</b></p>
                                </div>                                
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        <p class="font-weight-bold">Organiza, Inspira y Transforma: Herramientas para la Excelencia Educativa.</p>
                        
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

