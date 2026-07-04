@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">


            <div class="card-header p-1 alert-success">
                <div class="btn-group float-right pt-0 pb-2">
                    {{-- @include('administracion.pevaluacions.menus.index') --}}
                </div>
                <h4>Asignar Plan de Evaluación - Grupo Estables</h4>
            </div>

            <div class="card-body p-2 m-2">

                <div class="card">

                    <div class="card-header pb-0 mb-0 alert-secondary">
                        <h6>
                            <i class="{{ $icon_menus['chartarea'] ?? '' }} fa-1x text-primary"></i>
                            Grupos Estables Activos
                        </h6>
                    </div>

                    <div class="card-body">

                        {{-- @include('administracion.pevaluacions.indicadores.pevaluacion') --}}

                        <hr class="pt-1">

                        {{-- @include('administracion.pevaluacions.indicadores.seguimiento') --}}

                        {{-- <hr class="pt-1">

                        @include('administracion.pevaluacions.indicadores.progress_bars') --}}

                        {{-- <hr class="pt-1"> --}}

                        {{-- @include('administracion.pevaluacions.indicadores.charts') --}}

                    </div>
                </div>

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

{{-- {{ $seccions->links() }} --}}


@section('stylesheet')
    @parent
    <link rel="stylesheet" href="{{ asset('vendor/datatables/1.10.20/datatables/css/dataTables.bootstrap4.css') }}">
@endsection

@section('scripts')
    @parent
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/jquery.dataTables.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/dataTables.bootstrap4.js") }}"></script>
@endsection
