@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header p-1 alert-success">
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.pevaluacions.menus.index')
                </div>
                <h3>Plan de Evaluación</h3>
            </div>

            <div class="card-body p-2 m-2">

                <div class="card">
                    <div class="card-header pb-0 mb-0 alert-secondary">
                        <h6>
                            <i class="{{ $icon_menus['pevaluacion'] ?? '' }} fa-1x text-primary"></i>
                            Indicadores
                        </h6>
                    </div>
                    <div class="card-body">
                            <div class="border border-bottom-0 rounded-top">
                                <span class="pl-1 pt-1"><b>1ER LAPSO</b></span>
                            </div>
                            <div class="border border-top-0 rounded-bottom pb-2">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-6">
                                            @php $total_1 = $porc_asign_pe_1er; @endphp
                                            @php $total_2 = $porc_carga_pe_1er; @endphp
                                            @php $total_3 = $porc_carga_bol_1er; @endphp
                                            @includeif('administracion.pevaluacions.partials.info_box.pevaluacion')
                                        </div>
                                        <div class="col-6">
                                            {{-- @includeif('administracion.pevaluacions.partials.info_box.evaluativos') --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                            <hr>
                            <div class="border border-bottom-0 rounded-top">
                                <span class="pl-1 pt-1"><b>2DO LAPSO</b></span>
                            </div>
                            <div class="border border-top-0 rounded-bottom pb-2">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-6">
                                            @php $total_1 = 0; @endphp
                                            @php $total_2 = 0; @endphp
                                            @includeif('administracion.pevaluacions.partials.info_box.pevaluacion')
                                        </div>
                                        <div class="col-6">
                                                {{-- @includeif('administracion.pevaluacions.partials.info_box.evaluativos') --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                            <hr>
                            <div class="border border-bottom-0 rounded-top">
                                <span class="pl-1 pt-1"><b>3ER LAPSO</b></span>
                            </div>
                            <div class="border border-top-0 rounded-bottom pb-2">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-6">
                                            @php $total_1 = 0; @endphp
                                            @php $total_2 = 0; @endphp
                                            @includeif('administracion.pevaluacions.partials.info_box.pevaluacion')
                                        </div>
                                        <div class="col-6">
                                            {{-- @includeif('administracion.pevaluacions.partials.info_box.evaluativos') --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-12">
                                    @includeif('administracion.pevaluacions.partials.progress_bars.pevaluacions')
                                </div>
                            </div>
                            @control
                            <div class="row pt-2">
                                <div class="col-md-12 col-lg-6">
                                    @includeWhen((Auth::user()->IsControl()), 'administracion.inscripciones.chart.inscritoxgenero')
                                </div>
                                <div class="col-md-12 col-lg-6">
                                    @includeWhen((Auth::user()->IsControl()), 'administracion.inscripciones.chart.genderxplan')
                                </div>
                            </div>
                            @endcontrol
                        {{-- </div> --}}
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
