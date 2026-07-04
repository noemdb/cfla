@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">


            <div class="card-header p-1 alert-success">
                <div class="btn-group float-right pt-0 pb-2">
                    {{-- @include('administracion.pevaluacions.menus.index') --}}
                </div>
                <h4>Cifras Generales - ISLR. Período Escolar {{ Session::get('pescolar_name') }}</h4>
            </div>

            <div class="card-body p-2 m-2">

                <div class="card">

                    <div class="card-header pb-0 mb-0 alert-secondary">
                        <h6>
                            <i class="{{ $icon_menus['chartarea'] ?? '' }} fa-1x text-primary"></i>
                            Balance
                        </h6>
                    </div>

                    <div class="card-body">
                        <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Año 2020</a>
                            <a class="nav-item nav-link disabled" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Año 2021</a>
                            {{-- <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">Contact</a> --}}
                        </div>
                        </nav>
                            <div class="tab-content border border-top-0" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class=" p-2">
                                                @include('administracion.isrl.partials.balance.year_first.ammountCharged')
                                            </div>
                                            <div class=" p-2">
                                                @include('administracion.isrl.partials.balance.year_first.amountPending')
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class=" p-2">
                                                @include('administracion.isrl.partials.balance.year_first.resumen')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">...</div>
                            {{-- <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">...</div> --}}
                        </div>

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

{{-- @section('scripts')
    @parent
    <script src="{{ asset("js/Chart.bundle.js") }}"></script>
    <script src="{{ asset("js/ChartFunction.js") }}"></script>
    <script src="{{ asset("js/ChartEvent.js") }}"></script>
@endsection --}}


