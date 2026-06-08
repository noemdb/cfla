@extends('representants.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">
                <div class="btn-group float-right">
                    {{-- @include('representants.registropagos.menus.crud') --}}
                </div>
                <h4>
                    <u title="Listado especial con botones de acción">Listados</u> de <span class=" font-weight-bold ">Pagos</span>
                </h4>
                <small class=" font-weight-bold text-muted">
                    Los recibos generados en éste listado especial, son sólo de caracter informativo, para solicitar facturación cominiquese con la administración de la institución.
                </small>
            </div>

            <div class="card-body">

                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class=" border p-1">
                                <div class="alert alert-success rounded">
                                    Registros de Pagos relizados por la <span class=" font-weight-bold">Dirercción de Administración</span>
                                </div>
                                {{-- @include('representants.registropagos.table.crud') --}}
                                @include('representants.registropagos.table.crud.pagos')
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class=" border p-1">
                                <div class="alert alert-primary rounded">
                                    Reportes de pagos relizados por el <span class=" font-weight-bold">Representante</span>
                                </div>
                                @include('representants.registropagos.table.crud.reportes')
                            </div>
                        </div>

                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            @include('representants.home.charts.ingresoxmonth')
                        </div>
                        <div class="col-md-6">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-6">
                                        @include('representants.home.partials.boxes.morosidad')
                                    </div>
                                    <div class="col-md-6">
                                        @include('representants.home.partials.boxes.meetpayment')
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

        </div>
    </main>

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
