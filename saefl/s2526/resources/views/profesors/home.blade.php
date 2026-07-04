@extends('profesors.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="row">

            <div class="col-sm-9">

                @include('profesors.elements.messeges.oper_ok')

                @includeif('profesors.home.partials.indicadores')

                {{-- @include('profesors.modals.home.main') --}}
                
                <!-- Modal de Notificación de Diagnóstico -->
                @if(isset($mostrarModalNotificacion) && $mostrarModalNotificacion)
                <div class="">
                    @include('profesors.partials.modal-notificacion-diag')
                </div>
                    
                @endif

            </div>

            <div class="col-sm-3">
                {{-- @include('profesors.home.partials.helps') --}}
                
                <!-- Tarjeta Informativa para Profesor Guía -->
                @if(isset($esProfesorGuia) && $esProfesorGuia)
                    <div class="p-2 m-2">
                        @include('profesors.partials.card-profesor-guia')
                    </div>
                @endif
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

    <!-- Sweetalert2 para mensajes -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
@endsection