@extends('directors.layouts.dashboard.app')

@section('main')

    <main role="main">

        {{-- <div class="container pt-1 w-100"> --}}
            <div class="row">

                {{-- <div class="col-sm-3 mt-2">
                    @includeif('directors.card.profesor')
                </div> --}}

                <div class="col-sm-12">
                    @includeif('directors.home.partials.indicadores')
                </div>

            </div>
        {{-- </div> --}}

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
