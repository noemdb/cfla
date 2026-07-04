@extends('administracion.layouts.dashboard.app')

@section('title') Control de Asistencia - Carga de Marcajes @endsection

@section('main')

    <main role="main" class="col-md-10 ml-sm-auto col-lg-10">

        <div class="card card-primary mt-2">


            <div class="card-header pb-0 mb-0 alert-secondary">
                {{-- INI Menu rapido --}}
                <div class="btn-group float-right pt-0 pb-2"> @include('administracion.asisst_controls.assit_attendances.menus.index') </div>
                {{-- FIN Menu rapido --}}

                <h3>Guía instruccional - <span class="font-weight-bolder">Carga de Maracajes en el SAEFL</span></h3>
            </div>

            <div class="card-body">

                <h6 class=" font-weight-bolder">Secuencia de pasos:</h6>

                @include('administracion.asisst_controls.assit_attendances.help.loadCSV')

            </div>
        </div>
    </main>

@endsection
