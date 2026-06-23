@extends('academicos.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">

                <h3 class="mb-0 pb-0 text-uppercase">
                    <i class="{{$icon_menus['control_estudio'] ?? ''}} text-info" aria-hidden="true"></i>
                    Indicadores de <span class=" font-weight-bolder">Control de Estudio</span>
                </h3>
            </div>

            <div class="card-body p-1 m-1">

                <h5 class=" font-weight-bold">RENDIMIENTO ESTUDIANTIL</h5>

                <nav>
                    <div class="nav nav-tabs nav-fill font-weight-bold" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link show active text-left" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">
                            POR PLANES DE ESTUDIO
                        </a>
                        <a class="nav-item nav-link text-left" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">
                            POR AREAS DE CONOCIMIENTOS
                        </a>
                    </div>
                </nav>
                <div class="tab-content border border-top-0" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        <div class="p-3">
                            @include('academicos.performances/partials/index/pestudios')
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                        <div class="p-3">
                            @include('academicos.performances/partials/index/area_conocimientos')
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

@endsection

