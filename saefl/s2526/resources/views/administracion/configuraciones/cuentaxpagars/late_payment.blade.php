@extends('administracion.layouts.dashboard.app')

@section('title') - Morosidad por Planes de Estudio {{ Auth::user()->rol ?? '' }} @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">
            <div class="card-header pb-0 mb-0 alert-secondary">

                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2">
                    {{-- @include('administracion.configuraciones.cuentaxpagars.menus.account_bad') --}}
                </div>
                {{-- FIN Menu rapido --}}

                <h4>Indice de Morosidad por <span class="font-weight-bolder">Planes de Estudio</span> activos</h4>

            </div>

            <div class="card-body">

                @include('administracion.configuraciones.cuentaxpagars.partial.late_payment')

            </div>
        </div>
    </main>

@endsection

