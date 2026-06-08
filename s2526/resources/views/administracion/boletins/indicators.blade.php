@extends('administracion.layouts.dashboard.app')

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-secondary">
                <div class="btn-group float-right pt-0 pb-2">
                    @include('administracion.boletins.menus.indicators')
                </div>

                <h4><b class="text-dark">SEGUIMIENTO DE LA CARGA DE LAS NOTAS CORRESPONDIENTES A LOS PLANES DE EVALAUCIÓN</b> por momento de evaluación</h4>

            </div>

            <div class="card-body p-2 m-2">

                @include('administracion.boletins.indicators.seguimiento')

            </div>
        </div>
    </main>

@endsection
