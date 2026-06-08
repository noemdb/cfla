@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-info">
                {{-- INI Menu rapido --}}
                {{-- <div class="btn-group float-right"> --}}
                    {{-- @include('administracion.preinscripcions.menus.book') --}}
                {{-- </div> --}}
                {{-- FIN Menu rapido --}}
                <h3>
                    <i class="{{ $icon_menus['libro'] }} fa-1x text-info"></i>
                    Libro de Preinscripciones
                    <span class="float-right">
                            <a class="btn btn-dark" target="_blank"
                            href="{{ route('administracion.preinscripcions.book.pdf') }}" role="button">
                            <i class="fa fa-print" aria-hidden="true"></i>
                        </a>
                    </span> 
                </h3>
            </div>

            <div class="card-body">

                @isset($pestudios)
                    {{-- <h5 class="pt-1">Planes de Estudio</h5> --}}
                    @include('administracion.preinscripcions.book.tab.pestudios')
                @endisset

                @isset($grados)
                    {{-- <h5 class="pt-1">Grados</h5> --}}
                    @include('administracion.preinscripcions.book.tab.grados')                        
                @endisset                
                
                {{-- @include('administracion.preinscripcions.deck.preinscripcion') --}}
            
                {{-- deck con el los usuarios encontrados --}}                
                {{-- @include('administracion.preinscripcions.deck.preinscripcion')--}}

            {{-- @endisset --}}

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
