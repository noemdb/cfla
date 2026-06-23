@extends('administracion.layouts.home.app')

@section('main')

    <main role="main" class="col-md-10 col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">

                <div class="btn-group float-right">
                    {{-- @include('administracion.inscripciones.menus.index') --}}
                </div>

                <h4>
                    <i class="{{ $icon_menus['inscripciones'] }} fa-1x text-success"></i>
                    Indicadores Gerenciales
                </h4>

            </div>

            <div class="card-body p-1">

                @admon
                <div class="p-1">
                    <div class="row">
                        <div class="col-sm-12">
                            @includeIf('administracion.home.partials.labels.estudiantil')
                        </div>
                    </div>
                </div>
                <hr>
                @endadmon

                @admon
                <div class="p-1">
                    <div class="row">
                        <div class="col-sm-4 pr-0">
                            @includeIf('administracion.home.partials.labels.pay_goals')
                        </div>
                        <div class="col-sm-4 pr-0">
                            @includeIf('administracion.home.partials.labels.bill_state')
                        </div>
                        <div class="col-sm-4">
                            @includeIf('administracion.home.partials.labels.bill_grado')
                        </div>
                    </div>
                </div>
                <hr>
                @endadmon

                @admon
                <div class="p-1">
                    <div class="row">
                        <div class="col-md-12 col-lg-6">
                            @include('administracion.configuraciones.banco.chart.all_ingresoxmonth')
                        </div>
                        <div class="col-md-12 col-lg-6">
                            @include('administracion.configuraciones.banco.chart.all_ingresoxmetodo')
                        </div>
                    </div>
                </div>
                <hr>
                @endadmon

                @control
                <div class="p-1">
                    <div class="row">
                        <div class="col-sm-12">
                            @include('administracion.pevaluacions.evaluacions.chart.actividades')
                        </div>
                    </div>
                </div>
                <hr>
                @endcontrol

                @isset($pestudios)
                    <div class="p-1">
                        @admon
                        <div class="row">
                            <div class="col-md-12 col-lg-6">
                                @include('administracion.administrativas.chart.inscritoxgenero')
                            </div>
                            <div class="col-md-12 col-lg-6">
                                @include('administracion.administrativas.chart.genderxplan')
                            </div>
                        </div>
                        @endadmon
                        @control
                        <div class="row">
                            <div class="col-md-12 col-lg-6">
                                @includeWhen((Auth::user()->IsControl()), 'administracion.inscripciones.chart.inscritoxgenero')
                            </div>
                            <div class="col-md-12 col-lg-6">
                                @includeWhen((Auth::user()->IsControl()), 'administracion.inscripciones.chart.genderxplan')
                            </div>
                        </div>
                        @endcontrol
                    </div>
                    <hr>
                @endisset

                @isset($grados)
                    <div class="container p-1">
                        <div class="row">
                            <div class="col-md-12 col-lg-12">
                                {{-- @include('administracion.inscripciones.chart.inscritoxgeneroxgrado')  --}}
                                @includeWhen((Auth::user()->IsAdmon()), 'administracion.administrativas.chart.inscritoxgeneroxgrado')
                                @includeWhen((Auth::user()->IsControl()), 'administracion.inscripciones.chart.inscritoxgeneroxgrado')
                            </div>
                        </div>
                    </div>
                    <hr>
                @endisset

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

@section('stylesheet')
    @parent
    <link rel="stylesheet" href="{{ asset('vendor/datatables/1.10.20/datatables/css/dataTables.bootstrap4.css') }}">
@endsection

@section('scripts')
    @parent
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/jquery.dataTables.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/dataTables.bootstrap4.js") }}"></script>
@endsection
