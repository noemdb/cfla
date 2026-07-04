@extends('proyectos.layouts.dashboard.app')

@section('main')

    <main role="main">

        {{-- <div class="container pt-1 w-100"> --}}
            <div class="row">

                <div class="col-sm-12">
                    Coordinación de Proyecto.
                    {{-- @include('proyectos.elements.messeges.oper_ok') --}}
                    {{-- @includeif('proyectos.home.partials.indicadores') --}}
                </div>

            </div>
        {{-- </div> --}}

    </main>

    {{-- @include('proyectos.home.modals.main') --}}

@endsection

@section('chartsj')
    @parent
    <script src="{{ asset("js/Chart.bundle.js") }}"></script>
    <script src="{{ asset("js/ChartFunction.js") }}"></script>{{-- Funciones para generar los Chart --}}
    <script src="{{ asset("js/ChartEvent.js") }}"></script>{{-- Funciones para generar los Chart --}}
@endsection
