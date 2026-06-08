@extends('administracion.layouts.dashboard.app')

@section('title') Políticas de Cobranza - Inicio @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">


            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2"> @include('administracion.collections.coll_politicals.menus.index') </div>
                {{-- FIN Menu rapido --}}

                <h3><span class="font-weight-bolder">Políticas de Cobranza</span></h3>
            </div>

            <div class="card-body">

                @include('administracion.elements.messeges.oper_ok')

                @include('administracion.collections.coll_politicals.deck.index')

            </div>
        </div>
    </main>

@endsection


@section('scripts')
    @parent
    <script src="{{ asset("js/Chart.bundle.js") }}"></script>
    <script src="{{ asset("js/ChartFunction.js") }}"></script>{{-- Funciones para generar los Chart --}}
    <script src="{{ asset("js/ChartEvent.js") }}"></script>{{-- Funciones para generar los Chart --}}

@endsection
